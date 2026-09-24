<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SlugList;
use Illuminate\Support\Facades\Storage;

class SlugListController extends Controller
{
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
        $picture1Path = $this->findGlobalPicture('picture1');
        $picture2Path = $this->findGlobalPicture('picture2');

        return view('slug.index', compact(
            'slugs',
            'picture1Path',
            'picture2Path'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:255',
            'theme' => 'nullable|string|max:50',
        ]);

        SlugList::create([
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
            'theme' => $request->theme ?? 'violet',
        ]);

        return redirect()->route('slug.index')->with('success', 'Slug berhasil ditambahkan!');
    }

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
            'theme' => 'nullable|string|max:50',
        ]);

        $slug = SlugList::findOrFail($id);

        $slug->update([
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
            'theme' => $request->theme ?? $slug->theme,
        ]);

        return redirect()->route('slug.index')->with('success', 'Slug berhasil diperbarui!');
    }

    private function findGlobalPicture(string $picture): ?string
    {
        $legacyName = $picture === 'picture1' ? 'pria' : 'wanita';

        return collect(Storage::disk('public')->files('hero-defaults'))
            ->filter(function (string $path) use ($picture, $legacyName) {
                $base = basename($path);

                return str_starts_with($base, $picture . '.')
                    || str_starts_with($base, $legacyName . '.');
            })
            ->sortBy(fn (string $path) => str_starts_with(basename($path), $picture . '.') ? 0 : 1)
            ->first();
    }
}
