<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Galeri;

class GaleriController extends Controller
{
    public function store(Request $request, $slug_id)
    {
        // validasi file (max 5 per kategori)
        $request->validate([
            'carousel_atas.*'  => 'nullable|image|max:2048',
            'carousel_bawah.*' => 'nullable|image|max:2048',
        ]);

        $data = ['slug_list_id' => $slug_id];

        // simpan carousel atas
        if ($request->hasFile('carousel_atas')) {
            $atasPaths = [];
            foreach ($request->file('carousel_atas') as $file) {
                $atasPaths[] = $file->store('galeri/atas', 'public');
            }
            $data['carousel_atas'] = json_encode($atasPaths);
        }

        // simpan carousel bawah
        if ($request->hasFile('carousel_bawah')) {
            $bawahPaths = [];
            foreach ($request->file('carousel_bawah') as $file) {
                $bawahPaths[] = $file->store('galeri/bawah', 'public');
            }
            $data['carousel_bawah'] = json_encode($bawahPaths);
        }

        Galeri::updateOrCreate(
            ['slug_list_id' => $slug_id],
            $data
        );

        return back()->with('success', 'Galeri berhasil disimpan!');
    }
}
