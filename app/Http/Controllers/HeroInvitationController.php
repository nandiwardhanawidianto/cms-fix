<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\HeroInvitation;
use App\Models\SlugList;
use App\Models\Acara;
use App\Models\Galeri;
use App\Models\Lovegift;
use App\Models\Bank;
use App\Models\SongList;
use App\Models\Song;
use App\Models\KirimKado;
use App\Models\love_story;

class HeroInvitationController extends Controller
{
    public function edit($id)
    {
        $slug = SlugList::findOrFail($id);

        $heroInvitation = HeroInvitation::where('slug_id', $id)->first();

        // Load acara
        $acaras = Acara::where('slug_list_id', $id)->get();

        // Load galeri
        $galeri = Galeri::where('slug_list_id', $id)->first();

        // Load Love Gift
        $love_gifts = Lovegift::with('bank')
            ->where('slug_list_id', $id)
            ->get();

        // Load master bank
        $banks = Bank::all();

        // Load kirim kado
        $kirimkado = KirimKado::where('slug_list_id', $id)->first();

        // Load song
        $songs = Song::all();

        $selectedSongLists = SongList::with('song')
            ->where('slug_list_id', $id)
            ->get();

        // Load love story
        $lovestory = love_story::where('slug_list_id', $id)->first();

        return view('slug.edit', [
            'slug' => $slug,
            'slug_id' => $id,
            'heroInvitation' => $heroInvitation,
            'acaras' => $acaras,
            'galeri' => $galeri,
            'love_gifts' => $love_gifts,
            'kirimkado' => $kirimkado,
            'banks' => $banks,
            'songs' => $songs,
            'selectedSongLists' => $selectedSongLists,
            'lovestory' => $lovestory
        ]);
    }


    public function store(Request $request, $slug_id)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'nama_panggilan_pria' => 'nullable|string|max:255',
            'nama_lengkap_pria' => 'nullable|string|max:255',
            'orangtua_pria' => 'nullable|string|max:255',

            'nama_panggilan_wanita' => 'nullable|string|max:255',
            'nama_lengkap_wanita' => 'nullable|string|max:255',
            'orangtua_wanita' => 'nullable|string|max:255',

            // Hasil crop berupa Base64
            'foto_pria_cropped' => 'nullable|string',
            'foto_wanita_cropped' => 'nullable|string',
        ]);


        /*
        |--------------------------------------------------------------------------
        | AMBIL / BUAT HERO
        |--------------------------------------------------------------------------
        */

        $heroInvitation = HeroInvitation::firstOrNew([
            'slug_id' => $slug_id
        ]);


        /*
        |--------------------------------------------------------------------------
        | DATA PRIA
        |--------------------------------------------------------------------------
        */

        $heroInvitation->nama_panggilan_pria =
            $request->nama_panggilan_pria;

        $heroInvitation->nama_lengkap_pria =
            $request->nama_lengkap_pria;

        $heroInvitation->orangtua_pria =
            $request->orangtua_pria;


        /*
        |--------------------------------------------------------------------------
        | DATA WANITA
        |--------------------------------------------------------------------------
        */

        $heroInvitation->nama_panggilan_wanita =
            $request->nama_panggilan_wanita;

        $heroInvitation->nama_lengkap_wanita =
            $request->nama_lengkap_wanita;

        $heroInvitation->orangtua_wanita =
            $request->orangtua_wanita;


        /*
        |--------------------------------------------------------------------------
        | FOTO PRIA
        |--------------------------------------------------------------------------
        */

        if ($request->filled('foto_pria_cropped')) {

            $fotoPria = $this->saveCroppedImage(
                $request->foto_pria_cropped,
                'pria'
            );

            if ($fotoPria) {

                // Hapus foto lama jika ada
                if (!empty($heroInvitation->foto_pria)) {

                    if (
                        Storage::disk('public')
                            ->exists($heroInvitation->foto_pria)
                    ) {

                        Storage::disk('public')
                            ->delete($heroInvitation->foto_pria);
                    }
                }

                $heroInvitation->foto_pria = $fotoPria;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | FOTO WANITA
        |--------------------------------------------------------------------------
        */

        if ($request->filled('foto_wanita_cropped')) {

            $fotoWanita = $this->saveCroppedImage(
                $request->foto_wanita_cropped,
                'wanita'
            );

            if ($fotoWanita) {

                // Hapus foto lama jika ada
                if (!empty($heroInvitation->foto_wanita)) {

                    if (
                        Storage::disk('public')
                            ->exists($heroInvitation->foto_wanita)
                    ) {

                        Storage::disk('public')
                            ->delete($heroInvitation->foto_wanita);
                    }
                }

                $heroInvitation->foto_wanita = $fotoWanita;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN
        |--------------------------------------------------------------------------
        */

        $heroInvitation->slug_id = $slug_id;

        $heroInvitation->save();


        return back()->with(
            'success',
            'Hero & Invitation berhasil disimpan!'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN HASIL CROPPER
    |--------------------------------------------------------------------------
    |
    | Upload awal boleh PNG / JPG / JPEG.
    | Setelah crop, browser mengubah hasil menjadi JPEG.
    |
    */

    private function saveCroppedImage($base64Image, $jenis)
    {
        if (empty($base64Image)) {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan Base64 adalah gambar yang benar
        |--------------------------------------------------------------------------
        */

        if (
            !preg_match(
                '/^data:image\/(jpeg|jpg|png);base64,/',
                $base64Image
            )
        ) {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Hilangkan header Base64
        |--------------------------------------------------------------------------
        */

        $imageData = preg_replace(
            '/^data:image\/(jpeg|jpg|png);base64,/',
            '',
            $base64Image
        );


        $imageData = str_replace(
            ' ',
            '+',
            $imageData
        );


        /*
        |--------------------------------------------------------------------------
        | Decode
        |--------------------------------------------------------------------------
        */

        $decodedImage = base64_decode(
            $imageData,
            true
        );


        if ($decodedImage === false) {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Nama file
        |--------------------------------------------------------------------------
        */

        $filename =
            'hero/' .
            $jenis .
            '_' .
            time() .
            '_' .
            uniqid() .
            '.jpg';


        /*
        |--------------------------------------------------------------------------
        | Simpan ke storage/app/public/hero
        |--------------------------------------------------------------------------
        */

        Storage::disk('public')->put(
            $filename,
            $decodedImage
        );


        return $filename;
    }
}