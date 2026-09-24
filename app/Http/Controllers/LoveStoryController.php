<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\love_story;
use App\Models\SlugList;
use Illuminate\Support\Facades\Storage;

class LoveStoryController extends Controller
{
    public function edit($slug_id)
    {
        $slug = SlugList::findOrFail($slug_id);
        $lovestory = love_story::where('slug_list_id', $slug_id)->first();

        return view('slug.love_story', compact('slug_id', 'lovestory'));
    }

    public function store(Request $request, $slug_id)
    {
        $data = $request->validate([
            'judul_awal_pertemuan'    => 'nullable|string',
            'judul_menjalin_hubungan' => 'nullable|string',
            'judul_lamaran'           => 'nullable|string',
            'awal_pertemuan'          => 'nullable|string',
            'menjalin_hubungan'       => 'nullable|string',
            'lamaran'                 => 'nullable|string',

            // Backward compatibility for old plain file upload forms.
            'gambar_awal'             => 'nullable|image|max:10240',
            'gambar_hubungan'         => 'nullable|image|max:10240',
            'gambar_lamaran'          => 'nullable|image|max:10240',

            // New cropped images from the CMS cropper.
            'gambar_awal_cropped'     => 'nullable|string',
            'gambar_hubungan_cropped' => 'nullable|string',
            'gambar_lamaran_cropped'  => 'nullable|string',
        ]);

        $lovestory = love_story::where('slug_list_id', $slug_id)->first();

        if (!$lovestory) {
            $lovestory = new love_story();
            $lovestory->slug_list_id = $slug_id;
        }

        $photoFields = [
            'gambar_awal' => 'gambar_awal_cropped',
            'gambar_hubungan' => 'gambar_hubungan_cropped',
            'gambar_lamaran' => 'gambar_lamaran_cropped',
        ];

        foreach ($photoFields as $column => $croppedField) {
            $newPath = null;

            if ($request->filled($croppedField)) {
                $newPath = $this->saveBase64Image(
                    $request->input($croppedField),
                    'love_story'
                );

                if (!$newPath) {
                    return redirect()
                        ->to(route('slug.edit', $slug_id) . '#love_story')
                        ->withErrors([$croppedField => 'Hasil crop foto tidak valid. Silakan pilih dan crop ulang.'])
                        ->withInput();
                }
            } elseif ($request->hasFile($column)) {
                $newPath = $request->file($column)->store('love_story', 'public');
            }

            if ($newPath) {
                $oldPath = $lovestory->{$column};

                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }

                $data[$column] = $newPath;
            }

            unset($data[$croppedField]);
        }

        $lovestory->fill($data);
        $lovestory->save();

        return redirect()
            ->to(route('slug.edit', $slug_id) . '#love_story')
            ->with('success', 'Love Story berhasil disimpan!');
    }

    public function delete($slug_id)
    {
        $lovestory = love_story::where('slug_list_id', $slug_id)->first();

        if ($lovestory) {
            foreach (['gambar_awal', 'gambar_hubungan', 'gambar_lamaran'] as $column) {
                $path = $lovestory->{$column};

                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            $lovestory->delete();
        }

        return redirect()
            ->to(route('slug.edit', $slug_id) . '#love_story')
            ->with('success', 'Love Story berhasil dihapus!');
    }

    private function saveBase64Image(string $base64Image, string $folder): ?string
    {
        if (!preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/', $base64Image)) {
            return null;
        }

        $imageData = preg_replace(
            '/^data:image\/(jpeg|jpg|png|webp);base64,/',
            '',
            $base64Image
        );

        $decoded = base64_decode(str_replace(' ', '+', $imageData), true);

        if ($decoded === false) {
            return null;
        }

        $path = $folder . '/' . time() . '_' . uniqid() . '.jpg';

        return Storage::disk('public')->put($path, $decoded)
            ? $path
            : null;
    }
}
