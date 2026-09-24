<?php

namespace App\Http\Controllers;

use App\Models\Counting;
use App\Models\SlugList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CountingController extends Controller
{
    public function edit($slug_id)
    {
        $slug = SlugList::findOrFail($slug_id);
        $counting = Counting::where('slug_list_id', $slug_id)->first();

        return view('slug.counting', [
            'slug_id' => $slug_id,
            'slug'    => $slug,
            'counting'=> $counting,
        ]);
    }

    public function store(Request $request, $slug_id)
    {
        SlugList::findOrFail($slug_id);

        $validated = $request->validate([
            'nama_surat'            => 'nullable|string|max:255',
            'surat_arab'            => 'nullable|string',
            'deskripsi_surat'       => 'nullable|string',
            'foto_counting'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'foto_counting_cropped' => 'nullable|string',
        ]);

        $counting = Counting::firstOrNew(['slug_list_id' => $slug_id]);
        $counting->nama_surat = $validated['nama_surat'] ?? '';
        $counting->surat_arab = $validated['surat_arab'] ?? null;
        $counting->deskripsi_surat = $validated['deskripsi_surat'] ?? '';

        $newPhotoPath = null;

        if (!empty($validated['foto_counting_cropped'])) {
            $newPhotoPath = $this->saveCroppedCountdownImage($validated['foto_counting_cropped']);

            if (!$newPhotoPath) {
                throw ValidationException::withMessages([
                    'foto_counting_cropped' => 'Foto countdown hasil crop tidak valid.',
                ]);
            }
        } elseif ($request->hasFile('foto_counting')) {
            $newPhotoPath = $request->file('foto_counting')->store('counting', 'public');
        }

        if ($newPhotoPath) {
            $oldPhotoPath = $counting->foto_counting;
            $counting->foto_counting = $newPhotoPath;

            if (
                $oldPhotoPath
                && $oldPhotoPath !== $newPhotoPath
                && Storage::disk('public')->exists($oldPhotoPath)
            ) {
                Storage::disk('public')->delete($oldPhotoPath);
            }
        }

        $counting->slug_list_id = $slug_id;
        $counting->save();

        return redirect()
            ->to(route('slug.edit', $slug_id) . '#counting')
            ->with('success', 'Countdown berhasil disimpan!');
    }

    private function saveCroppedCountdownImage(string $base64Image): ?string
    {
        if (!preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/', $base64Image)) {
            return null;
        }

        $imageData = preg_replace('/^data:image\/(jpeg|jpg|png|webp);base64,/', '', $base64Image);
        $imageData = str_replace(' ', '+', $imageData);
        $decodedImage = base64_decode($imageData, true);

        if ($decodedImage === false || strlen($decodedImage) > 10 * 1024 * 1024) {
            return null;
        }

        $filename = 'counting/countdown_' . time() . '_' . uniqid() . '.jpg';

        if (!Storage::disk('public')->put($filename, $decodedImage)) {
            return null;
        }

        return $filename;
    }
}
