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

            'gambar_awal'         => 'nullable|image|max:2048',
            'gambar_hubungan'     => 'nullable|image|max:2048',
            'gambar_lamaran'      => 'nullable|image|max:2048',
        ]);

        $lovestory = love_story::where('slug_list_id', $slug_id)->first();

        if (!$lovestory) {
            $lovestory = new love_story();
            $lovestory->slug_list_id = $slug_id;
        }

        // ============================
        // 🔥 GANTI GAMBAR (Replace lama)
        // ============================

        // Gambar Awal
        if ($request->hasFile('gambar_awal')) {

            // hapus gambar lama
            if ($lovestory->gambar_awal && Storage::disk('public')->exists($lovestory->gambar_awal)) {
                Storage::disk('public')->delete($lovestory->gambar_awal);
            }

            // upload yang baru
            $data['gambar_awal'] = $request->file('gambar_awal')->store('love_story', 'public');
        }

        // Gambar Hubungan
        if ($request->hasFile('gambar_hubungan')) {

            if ($lovestory->gambar_hubungan && Storage::disk('public')->exists($lovestory->gambar_hubungan)) {
                Storage::disk('public')->delete($lovestory->gambar_hubungan);
            }

            $data['gambar_hubungan'] = $request->file('gambar_hubungan')->store('love_story', 'public');
        }

        // Gambar Lamaran
        if ($request->hasFile('gambar_lamaran')) {

            if ($lovestory->gambar_lamaran && Storage::disk('public')->exists($lovestory->gambar_lamaran)) {
                Storage::disk('public')->delete($lovestory->gambar_lamaran);
            }

            $data['gambar_lamaran'] = $request->file('gambar_lamaran')->store('love_story', 'public');
        }

        // Simpan data
        $lovestory->fill($data);
        $lovestory->save();

        return redirect()->back()->with('success', 'Love Story berhasil disimpan!');
    }

    public function delete($slug_id)
    {
        $lovestory = love_story::where('slug_list_id', $slug_id)->first();

        if ($lovestory) {

            // Hapus semua file gambar
            if ($lovestory->gambar_awal) {
                Storage::disk('public')->delete($lovestory->gambar_awal);
            }
            if ($lovestory->gambar_hubungan) {
                Storage::disk('public')->delete($lovestory->gambar_hubungan);
            }
            if ($lovestory->gambar_lamaran) {
                Storage::disk('public')->delete($lovestory->gambar_lamaran);
            }

            $lovestory->delete();
        }

        return redirect()->back()->with('success', 'Love Story berhasil dihapus!');
    }
}
