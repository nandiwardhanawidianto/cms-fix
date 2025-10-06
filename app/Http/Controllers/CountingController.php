<?php

namespace App\Http\Controllers;

use App\Models\Counting;
use App\Models\SlugList;
use Illuminate\Http\Request;

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
        $validated = $request->validate([
            'nama_surat'      => 'required|string|max:255',
            'surat_arab'      => 'nullable|string',
            'deskripsi_surat' => 'required|string',
        ]);

        Counting::updateOrCreate(
            ['slug_list_id' => $slug_id],
            [
                'nama_surat'      => $validated['nama_surat'],
                'surat_arab'      => $validated['surat_arab'] ?? null,
                'deskripsi_surat' => $validated['deskripsi_surat'],
            ]
        );

        return back()->with('success', 'Counting berhasil disimpan!');
    }
}
