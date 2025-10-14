<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\SongList;
use App\Models\SlugList;
use Illuminate\Http\Request;

class SongListController extends Controller
{
    public function index($slug_list_id)
    {
        $slug = SlugList::findOrFail($slug_list_id);
        $songs = Song::all();
        $selectedSongs = SongList::where('slug_list_id', $slug_list_id)->pluck('song_id')->toArray();

        return view('slug.song_list', compact('slug', 'songs', 'selectedSongs'));
    }

    public function store(Request $request, $slug_list_id)
    {
        $request->validate([
            'song_id' => 'required|exists:songs,id',
        ]);

        SongList::create([
            'slug_list_id' => $slug_list_id,
            'song_id' => $request->song_id,
        ]);

        return back()->with('success', 'Lagu berhasil ditambahkan ke slug!');
    }

    public function destroy(SongList $songList)
    {
        $songList->delete();
        return back()->with('success', 'Lagu berhasil dihapus dari daftar!');
    }
}
