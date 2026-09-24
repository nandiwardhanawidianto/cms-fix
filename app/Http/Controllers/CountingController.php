<?php

namespace App\Http\Controllers;

use App\Models\Counting;
use App\Models\SlugList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'nama_surat'      => 'required|string|max:255',
            'surat_arab'      => 'nullable|string',
            'deskripsi_surat' => 'required|string',
            'foto_counting'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $counting = Counting::firstOrNew(['slug_list_id' => $slug_id]);
        $counting->nama_surat = $validated['nama_surat'];
        $counting->surat_arab = $validated['surat_arab'] ?? null;
        $counting->deskripsi_surat = $validated['deskripsi_surat'];

        if ($request->hasFile('foto_counting')) {
            if ($counting->foto_counting && Storage::disk('public')->exists($counting->foto_counting)) {
                Storage::disk('public')->delete($counting->foto_counting);
            }

            $counting->foto_counting = $request->file('foto_counting')->store(
                'counting',
                'public'
            );
        }

        $counting->slug_list_id = $slug_id;
        $counting->save();

        return redirect()
            ->to(route('slug.edit', $slug_id) . '#counting')
            ->with('success', 'Countdown berhasil disimpan!');
    }
}
