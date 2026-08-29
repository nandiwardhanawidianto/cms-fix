<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Galeri;

class GaleriController extends Controller
{
    public function store(Request $request, $slug_id)
    {
        $request->validate([
            'carousel_atas_cropped' => 'nullable|array|max:5',
            'carousel_atas_cropped.*' => 'nullable|string',

            'carousel_bawah_cropped' => 'nullable|array|max:5',
            'carousel_bawah_cropped.*' => 'nullable|string',
        ]);

        // Ambil galeri lama
        $galeri = Galeri::where('slug_list_id', $slug_id)->first();

        $data = [
            'slug_list_id' => $slug_id
        ];


        /*
        |--------------------------------------------------------------------------
        | CAROUSEL ATAS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('carousel_atas_cropped')) {

            $newPaths = [];

            // Simpan semua foto baru dulu
            foreach ($request->carousel_atas_cropped as $image) {

                $path = $this->saveBase64Image(
                    $image,
                    'galeri/atas'
                );

                if ($path) {
                    $newPaths[] = $path;
                }
            }


            // Kalau foto baru berhasil disimpan
            if (count($newPaths) > 0) {

                // Hapus foto lama
                if ($galeri && !empty($galeri->carousel_atas)) {

                    $oldImages = json_decode(
                        $galeri->carousel_atas,
                        true
                    );

                    if (is_array($oldImages)) {

                        foreach ($oldImages as $oldImage) {

                            if (
                                Storage::disk('public')
                                    ->exists($oldImage)
                            ) {
                                Storage::disk('public')
                                    ->delete($oldImage);
                            }
                        }
                    }
                }

                // Masukkan path baru ke database
                $data['carousel_atas'] = json_encode(
                    $newPaths
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CAROUSEL BAWAH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('carousel_bawah_cropped')) {

            $newPaths = [];

            // Simpan semua foto baru dulu
            foreach ($request->carousel_bawah_cropped as $image) {

                $path = $this->saveBase64Image(
                    $image,
                    'galeri/bawah'
                );

                if ($path) {
                    $newPaths[] = $path;
                }
            }


            // Kalau foto baru berhasil disimpan
            if (count($newPaths) > 0) {

                // Hapus foto lama
                if ($galeri && !empty($galeri->carousel_bawah)) {

                    $oldImages = json_decode(
                        $galeri->carousel_bawah,
                        true
                    );

                    if (is_array($oldImages)) {

                        foreach ($oldImages as $oldImage) {

                            if (
                                Storage::disk('public')
                                    ->exists($oldImage)
                            ) {
                                Storage::disk('public')
                                    ->delete($oldImage);
                            }
                        }
                    }
                }

                // Masukkan path baru ke database
                $data['carousel_bawah'] = json_encode(
                    $newPaths
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE / CREATE DATABASE
        |--------------------------------------------------------------------------
        */

        Galeri::updateOrCreate(
            [
                'slug_list_id' => $slug_id
            ],
            $data
        );


        return back()->with(
            'success',
            'Galeri berhasil disimpan!'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN HASIL CROP BASE64
    |--------------------------------------------------------------------------
    */

    private function saveBase64Image($base64Image, $folder)
    {
        // Pastikan format benar
        if (
            !preg_match(
                '/^data:image\/(jpeg|jpg|png);base64,/',
                $base64Image
            )
        ) {
            return null;
        }


        // Hilangkan header Base64
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


        // Decode Base64
        $decoded = base64_decode(
            $imageData,
            true
        );


        if ($decoded === false) {
            return null;
        }


        // Nama file unik
        $filename =
            $folder .
            '/' .
            time() .
            '_' .
            uniqid() .
            '.jpg';


        // Simpan ke storage/app/public/
        $saved = Storage::disk('public')->put(
            $filename,
            $decoded
        );


        if (!$saved) {
            return null;
        }


        return $filename;
    }
}