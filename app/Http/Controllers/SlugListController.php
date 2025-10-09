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

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where('nama', 'like', "%{$keyword}%")
                  ->orWhere('slug', 'like', "%{$keyword}%")
                  ->orWhere('keterangan', 'like', "%{$keyword}%");
        }

        $slugs = $query->orderBy('id', 'desc')->get();

        return view('slug.index', compact('slugs'));
    }

    // Simpan slug baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:255',
            'theme' => 'nullable|string|max:50', // ✅ tambahkan validasi theme
        ]);

        SlugList::create([
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
            'theme' => $request->theme ?? 'violet', // ✅ default theme violet
        ]);

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
            'theme' => 'nullable|string|max:50', // ✅ validasi tambahan
        ]);

        $slug = SlugList::findOrFail($id);

        $slug->update([
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
            'theme' => $request->theme ?? $slug->theme, // ✅ update theme
        ]);

        return redirect()->route('slug.index')->with('success', 'Slug berhasil diperbarui!');
    }
}
