<?php

namespace App\Http\Controllers;

use App\Models\Acara;
use App\Models\Bank;
use App\Models\Counting;
use App\Models\Galeri;
use App\Models\HeroInvitation;
use App\Models\KirimKado;
use App\Models\Lovegift;
use App\Models\SlugList;
use App\Models\Song;
use App\Models\SongList;
use App\Models\love_story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class HeroInvitationController extends Controller
{
    public function edit($id)
    {
        $slug = SlugList::findOrFail($id);
        $heroInvitation = HeroInvitation::where('slug_id', $id)->first();

        $acaras = Acara::where('slug_list_id', $id)->get();
        $counting = Counting::where('slug_list_id', $id)->first();
        $galeri = Galeri::where('slug_list_id', $id)->first();
        $love_gifts = Lovegift::with('bank')->where('slug_list_id', $id)->get();
        $banks = Bank::all();
        $kirimkado = KirimKado::where('slug_list_id', $id)->first();
        $songs = Song::all();
        $selectedSongLists = SongList::with('song')->where('slug_list_id', $id)->get();
        $lovestory = love_story::where('slug_list_id', $id)->first();

        return view('slug.edit', [
            'slug' => $slug,
            'slug_id' => $id,
            'heroInvitation' => $heroInvitation,
            'acaras' => $acaras,
            'counting' => $counting,
            'galeri' => $galeri,
            'love_gifts' => $love_gifts,
            'kirimkado' => $kirimkado,
            'banks' => $banks,
            'songs' => $songs,
            'selectedSongLists' => $selectedSongLists,
            'lovestory' => $lovestory,
            'picture1Path' => $this->findGlobalPicture('picture1'),
            'picture2Path' => $this->findGlobalPicture('picture2'),
        ]);
    }

    public function store(Request $request, $slug_id)
    {
        $request->validate([
            'nama_panggilan_pria' => 'nullable|string|max:255',
            'nama_lengkap_pria' => 'nullable|string|max:255',
            'orangtua_pria' => 'nullable|string|max:255',
            'nama_panggilan_wanita' => 'nullable|string|max:255',
            'nama_lengkap_wanita' => 'nullable|string|max:255',
            'orangtua_wanita' => 'nullable|string|max:255',
            'foto_pria_source' => 'nullable|in:keep,upload,picture1,picture2',
            'foto_wanita_source' => 'nullable|in:keep,upload,picture1,picture2',
            'foto_pria_cropped' => 'nullable|string',
            'foto_wanita_cropped' => 'nullable|string',
        ]);

        SlugList::findOrFail($slug_id);
        $heroInvitation = HeroInvitation::firstOrNew(['slug_id' => $slug_id]);

        $heroInvitation->nama_panggilan_pria = $request->nama_panggilan_pria;
        $heroInvitation->nama_lengkap_pria = $request->nama_lengkap_pria;
        $heroInvitation->orangtua_pria = $request->orangtua_pria;
        $heroInvitation->nama_panggilan_wanita = $request->nama_panggilan_wanita;
        $heroInvitation->nama_lengkap_wanita = $request->nama_lengkap_wanita;
        $heroInvitation->orangtua_wanita = $request->orangtua_wanita;

        $this->applyPhotoSelection(
            $heroInvitation,
            'foto_pria',
            $request->input('foto_pria_source', 'keep'),
            $request->input('foto_pria_cropped'),
            'pria'
        );

        $this->applyPhotoSelection(
            $heroInvitation,
            'foto_wanita',
            $request->input('foto_wanita_source', 'keep'),
            $request->input('foto_wanita_cropped'),
            'wanita'
        );

        $heroInvitation->slug_id = $slug_id;
        $heroInvitation->save();

        return redirect()
            ->to(route('slug.edit', $slug_id) . '#hero')
            ->with('success', 'Hero & Invitation berhasil disimpan!');
    }

    public function storeDefaultPhoto(Request $request, string $picture)
    {
        $this->validatePictureKey($picture);

        $request->validate([
            'default_photo' => 'required|image|mimes:jpg,jpeg,png|max:10240',
        ]);

        $disk = Storage::disk('public');
        $oldPaths = $this->globalPicturePaths($picture);
        $extension = strtolower($request->file('default_photo')->extension());

        $newPath = $request->file('default_photo')->storeAs(
            'hero-defaults',
            $picture . '.' . $extension,
            'public'
        );

        if (!$newPath) {
            throw ValidationException::withMessages([
                'default_photo' => 'Gambar gagal disimpan.',
            ]);
        }

        if ($oldPaths) {
            HeroInvitation::whereIn('foto_pria', $oldPaths)->update([
                'foto_pria' => $newPath,
            ]);

            HeroInvitation::whereIn('foto_wanita', $oldPaths)->update([
                'foto_wanita' => $newPath,
            ]);
        }

        foreach ($oldPaths as $oldPath) {
            if ($oldPath !== $newPath && $disk->exists($oldPath)) {
                $disk->delete($oldPath);
            }
        }

        $label = $picture === 'picture1' ? 'Picture 1' : 'Picture 2';

        return redirect()
            ->to(route('slug.index') . '#default-photos')
            ->with('success', $label . ' berhasil disimpan.');
    }

    private function applyPhotoSelection(
        HeroInvitation $heroInvitation,
        string $column,
        string $source,
        ?string $croppedImage,
        string $uploadPrefix
    ): void {
        if ($source === 'keep') {
            return;
        }

        if ($source === 'upload') {
            if (!$croppedImage) {
                throw ValidationException::withMessages([
                    $column . '_cropped' => 'Pilih dan crop foto terlebih dahulu.',
                ]);
            }

            $newPath = $this->saveCroppedImage($croppedImage, $uploadPrefix);

            if (!$newPath) {
                throw ValidationException::withMessages([
                    $column . '_cropped' => 'Foto hasil crop tidak valid.',
                ]);
            }

            $this->deleteOwnedPhoto($heroInvitation->{$column});
            $heroInvitation->{$column} = $newPath;
            return;
        }

        if (in_array($source, ['picture1', 'picture2'], true)) {
            $picturePath = $this->findGlobalPicture($source);

            if (!$picturePath) {
                throw ValidationException::withMessages([
                    $column . '_source' => ($source === 'picture1' ? 'Picture 1' : 'Picture 2') . ' belum diupload dari halaman Management Undangan.',
                ]);
            }

            $this->deleteOwnedPhoto($heroInvitation->{$column});
            $heroInvitation->{$column} = $picturePath;
        }
    }

    private function saveCroppedImage($base64Image, $jenis)
    {
        if (empty($base64Image)) {
            return null;
        }

        if (!preg_match('/^data:image\/(jpeg|jpg|png);base64,/', $base64Image)) {
            return null;
        }

        $imageData = preg_replace('/^data:image\/(jpeg|jpg|png);base64,/', '', $base64Image);
        $imageData = str_replace(' ', '+', $imageData);
        $decodedImage = base64_decode($imageData, true);

        if ($decodedImage === false) {
            return null;
        }

        $filename = 'hero/' . $jenis . '_' . time() . '_' . uniqid() . '.jpg';
        Storage::disk('public')->put($filename, $decodedImage);

        return $filename;
    }

    private function validatePictureKey(string $picture): void
    {
        if (!in_array($picture, ['picture1', 'picture2'], true)) {
            abort(404);
        }
    }

    private function findGlobalPicture(string $picture): ?string
    {
        return $this->globalPicturePaths($picture)[0] ?? null;
    }

    private function globalPicturePaths(string $picture): array
    {
        $legacyName = $picture === 'picture1' ? 'pria' : 'wanita';

        return collect(Storage::disk('public')->files('hero-defaults'))
            ->filter(function (string $path) use ($picture, $legacyName) {
                $base = basename($path);

                return str_starts_with($base, $picture . '.')
                    || str_starts_with($base, $legacyName . '.');
            })
            ->sortBy(fn (string $path) => str_starts_with(basename($path), $picture . '.') ? 0 : 1)
            ->values()
            ->all();
    }

    private function isSharedPicture(?string $path): bool
    {
        return is_string($path) && str_starts_with($path, 'hero-defaults/');
    }

    private function deleteOwnedPhoto(?string $path): void
    {
        if (!$path || $this->isSharedPicture($path)) {
            return;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($path)) {
            $disk->delete($path);
        }
    }
}
