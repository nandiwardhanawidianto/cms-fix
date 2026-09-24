<?php

namespace App\Http\Controllers;

use App\Models\Acara;
use App\Models\Bank;
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
            'galeri' => $galeri,
            'love_gifts' => $love_gifts,
            'kirimkado' => $kirimkado,
            'banks' => $banks,
            'songs' => $songs,
            'selectedSongLists' => $selectedSongLists,
            'lovestory' => $lovestory,
            'defaultFotoPria' => $this->findDefaultPhoto('pria'),
            'defaultFotoWanita' => $this->findDefaultPhoto('wanita'),
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
            'foto_pria_cropped' => 'nullable|string',
            'foto_wanita_cropped' => 'nullable|string',
        ]);

        $heroInvitation = HeroInvitation::firstOrNew(['slug_id' => $slug_id]);

        $heroInvitation->nama_panggilan_pria = $request->nama_panggilan_pria;
        $heroInvitation->nama_lengkap_pria = $request->nama_lengkap_pria;
        $heroInvitation->orangtua_pria = $request->orangtua_pria;
        $heroInvitation->nama_panggilan_wanita = $request->nama_panggilan_wanita;
        $heroInvitation->nama_lengkap_wanita = $request->nama_lengkap_wanita;
        $heroInvitation->orangtua_wanita = $request->orangtua_wanita;

        if ($request->filled('foto_pria_cropped')) {
            $fotoPria = $this->saveCroppedImage($request->foto_pria_cropped, 'pria');

            if ($fotoPria) {
                $this->deleteOwnedPhoto($heroInvitation->foto_pria);
                $heroInvitation->foto_pria = $fotoPria;
            }
        }

        if ($request->filled('foto_wanita_cropped')) {
            $fotoWanita = $this->saveCroppedImage($request->foto_wanita_cropped, 'wanita');

            if ($fotoWanita) {
                $this->deleteOwnedPhoto($heroInvitation->foto_wanita);
                $heroInvitation->foto_wanita = $fotoWanita;
            }
        }

        $this->syncAutomaticDefaultPhoto($heroInvitation, 'pria');
        $this->syncAutomaticDefaultPhoto($heroInvitation, 'wanita');

        $heroInvitation->slug_id = $slug_id;
        $heroInvitation->save();

        return back()->with('success', 'Hero & Invitation berhasil disimpan!');
    }

    /**
     * Kept for backwards compatibility with older UI links.
     * New workflow applies defaults automatically.
     */
    public function useDefaultPhoto($slug_id, string $type)
    {
        $this->validatePhotoType($type);
        SlugList::findOrFail($slug_id);

        $defaultPath = $this->findDefaultPhoto($type);

        if (!$defaultPath) {
            throw ValidationException::withMessages([
                'default_photo' => 'Foto default belum diupload untuk mempelai ini.',
            ]);
        }

        $heroInvitation = HeroInvitation::firstOrNew(['slug_id' => $slug_id]);
        $column = $type === 'pria' ? 'foto_pria' : 'foto_wanita';

        $this->deleteOwnedPhoto($heroInvitation->{$column});

        $heroInvitation->slug_id = $slug_id;
        $heroInvitation->{$column} = $defaultPath;
        $heroInvitation->save();

        return redirect()
            ->to(route('slug.edit', $slug_id) . '#hero')
            ->with('success', 'Foto default berhasil dipakai untuk undangan ini.');
    }

    public function storeDefaultPhoto(Request $request, string $type)
    {
        $this->validatePhotoType($type);

        $request->validate([
            'default_photo' => 'required|image|mimes:jpg,jpeg,png|max:10240',
        ]);

        $disk = Storage::disk('public');
        $oldPaths = $this->defaultPhotoPaths($type);
        $extension = strtolower($request->file('default_photo')->extension());
        $newPath = $request->file('default_photo')->storeAs(
            'hero-defaults',
            $type . '.' . $extension,
            'public'
        );

        if (!$newPath) {
            throw ValidationException::withMessages([
                'default_photo' => 'Foto default gagal disimpan.',
            ]);
        }

        $column = $type === 'pria' ? 'foto_pria' : 'foto_wanita';
        $fullNameColumn = $type === 'pria' ? 'nama_lengkap_pria' : 'nama_lengkap_wanita';
        $shortNameColumn = $type === 'pria' ? 'nama_panggilan_pria' : 'nama_panggilan_wanita';

        HeroInvitation::where(function ($query) use ($column, $fullNameColumn, $shortNameColumn, $oldPaths) {
            if ($oldPaths) {
                $query->whereIn($column, $oldPaths)
                    ->orWhere(function ($emptyPhoto) use ($column, $fullNameColumn, $shortNameColumn) {
                        $emptyPhoto->whereNull($column)
                            ->where(function ($person) use ($fullNameColumn, $shortNameColumn) {
                                $person->whereNotNull($fullNameColumn)
                                    ->orWhereNotNull($shortNameColumn);
                            });
                    });
                return;
            }

            $query->whereNull($column)
                ->where(function ($person) use ($fullNameColumn, $shortNameColumn) {
                    $person->whereNotNull($fullNameColumn)
                        ->orWhereNotNull($shortNameColumn);
                });
        })->update([
            $column => $newPath,
        ]);

        foreach ($oldPaths as $oldPath) {
            if ($oldPath !== $newPath && $disk->exists($oldPath)) {
                $disk->delete($oldPath);
            }
        }

        return redirect()
            ->to(route('slug.index') . '#default-photos')
            ->with(
                'success',
                'Foto default ' . ($type === 'pria' ? 'Mempelai 1' : 'Mempelai 2') . ' berhasil disimpan. Undangan tanpa foto custom otomatis memakai default ini.'
            );
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

    private function validatePhotoType(string $type): void
    {
        if (!in_array($type, ['pria', 'wanita'], true)) {
            abort(404);
        }
    }

    private function findDefaultPhoto(string $type): ?string
    {
        return $this->defaultPhotoPaths($type)[0] ?? null;
    }

    private function defaultPhotoPaths(string $type): array
    {
        return collect(Storage::disk('public')->files('hero-defaults'))
            ->filter(fn (string $path) => str_starts_with(basename($path), $type . '.'))
            ->values()
            ->all();
    }

    private function syncAutomaticDefaultPhoto(HeroInvitation $heroInvitation, string $type): void
    {
        $photoColumn = $type === 'pria' ? 'foto_pria' : 'foto_wanita';
        $fullNameColumn = $type === 'pria' ? 'nama_lengkap_pria' : 'nama_lengkap_wanita';
        $shortNameColumn = $type === 'pria' ? 'nama_panggilan_pria' : 'nama_panggilan_wanita';
        $hasPerson = filled($heroInvitation->{$fullNameColumn}) || filled($heroInvitation->{$shortNameColumn});

        if (!$hasPerson) {
            if ($this->isSharedDefaultPhoto($heroInvitation->{$photoColumn})) {
                $heroInvitation->{$photoColumn} = null;
            }
            return;
        }

        if (!filled($heroInvitation->{$photoColumn})) {
            $defaultPath = $this->findDefaultPhoto($type);
            if ($defaultPath) {
                $heroInvitation->{$photoColumn} = $defaultPath;
            }
        }
    }

    private function isSharedDefaultPhoto(?string $path): bool
    {
        return is_string($path) && str_starts_with($path, 'hero-defaults/');
    }

    private function deleteOwnedPhoto(?string $path): void
    {
        if (!$path || $this->isSharedDefaultPhoto($path)) {
            return;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($path)) {
            $disk->delete($path);
        }
    }
}
