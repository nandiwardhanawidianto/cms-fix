<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\SongList;
use App\Models\SlugList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SongListController extends Controller
{
    public function index($slug_list_id)
    {
        $slug = SlugList::findOrFail($slug_list_id);
        $songs = Song::orderBy('title')->get();
        $selectedSongLists = SongList::with('song')
            ->where('slug_list_id', $slug_list_id)
            ->get();

        return view('slug.song_list', compact(
            'slug',
            'songs',
            'selectedSongLists'
        ));
    }

    public function store(Request $request, $slug_list_id)
    {
        SlugList::findOrFail($slug_list_id);

        $request->validate([
            'song_id' => 'required|exists:songs,id',
        ]);

        SongList::firstOrCreate([
            'slug_list_id' => $slug_list_id,
            'song_id' => $request->song_id,
        ]);

        return redirect()
            ->to(route('slug.edit', $slug_list_id) . '#song_list')
            ->with('success', 'Lagu berhasil ditambahkan ke slug!');
    }

    public function storeUploadedSong(Request $request, $slug_list_id)
    {
        SlugList::findOrFail($slug_list_id);

        $validator = Validator::make($request->all(), [
            'new_song_title' => 'required|string|max:255',
            'new_song_file' => 'required|file|mimes:mp3,wav,ogg|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to(route('slug.edit', $slug_list_id) . '#song_list')
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('new_song_file');
        $path = $file->store('songs', 'public');

        if (!$path) {
            return redirect()
                ->to(route('slug.edit', $slug_list_id) . '#song_list')
                ->withErrors(['new_song_file' => 'File lagu gagal disimpan.'])
                ->withInput();
        }

        try {
            DB::transaction(function () use ($request, $slug_list_id, $path) {
                $song = Song::create([
                    'title' => trim($request->new_song_title),
                    'file_path' => $path,
                ]);

                SongList::firstOrCreate([
                    'slug_list_id' => $slug_list_id,
                    'song_id' => $song->id,
                ]);
            });
        } catch (\Throwable $e) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            throw $e;
        }

        return redirect()
            ->to(route('slug.edit', $slug_list_id) . '#song_list')
            ->with('success', 'Lagu baru berhasil diupload ke Master Lagu dan langsung dipilih untuk undangan ini.');
    }

    public function destroy(SongList $songList)
    {
        $slugId = $songList->slug_list_id;
        $songList->delete();

        return redirect()
            ->to(route('slug.edit', $slugId) . '#song_list')
            ->with('success', 'Lagu berhasil dihapus dari daftar!');
    }
}
