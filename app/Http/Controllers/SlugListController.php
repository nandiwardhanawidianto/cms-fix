<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SlugList;

class SlugListController extends Controller
{
    // Tampilkan halaman slug list
    public function index(Request $request)
    {
        $query = SlugList::query();

        // kalau ada input search
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where('nama', 'like', "%{$keyword}%")
                  ->orWhere('slug', 'like', "%{$keyword}%")
                  ->orWhere('keterangan', 'like', "%{$keyword}%");
        }

        // urutkan terbaru dulu
        $slugs = $query->orderBy('id', 'desc')->get();

        return view('slug.index', compact('slugs'));
    }

    // Simpan slug baru
    public function store(Request $request)
    {
        // validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // simpan data slug
        SlugList::create([
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
        ]);

        // redirect balik ke halaman slug-list
        return redirect()->route('slug.index')->with('success', 'Slug berhasil ditambahkan!');
    }

    // Hapus slug
    public function destroy($id)
    {
        $slug = SlugList::findOrFail($id);
        $slug->delete();

        return redirect()->route('slug.index')->with('success', 'Slug berhasil dihapus!');
    }

    public function edit($id)
    {
        $slug = SlugList::findOrFail($id);
        return view('slug.edit', compact('slug'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $slug = SlugList::findOrFail($id);

        $slug->update([
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
            // hosting_at jangan diubah dulu, kita kasih opsi di tombol terpisah
        ]);

        return redirect()->route('slug.index')->with('success', 'Slug berhasil diperbarui!');
}

}
