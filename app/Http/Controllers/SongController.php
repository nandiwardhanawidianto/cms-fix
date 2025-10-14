<?php
namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SongController extends Controller
{
    public function index()
    {
        $songs = Song::all();
        return view('/slug/song', compact('songs'));
    }
    
    public function store(Request $request)
{
    \Log::debug('===== MULAI UPLOAD LAGU =====');
    \Log::debug('Request data:', $request->all());

    $request->validate([
        'title' => 'required|string|max:255',
        'file' => 'required|file|mimes:mp3,wav,ogg|max:10240',
    ]);

    if (!$request->hasFile('file')) {
        \Log::error('❌ Tidak ada file diterima!');
        return back()->withErrors(['file' => 'File tidak terkirim ke server.']);
    }

    $file = $request->file('file');

    if (!$file->isValid()) {
        \Log::error('❌ File tidak valid: ' . $file->getErrorMessage());
        return back()->withErrors(['file' => 'File gagal diupload: ' . $file->getErrorMessage()]);
    }
    \Log::debug('=== TEST UPLOAD MULAI ===');
    \Log::debug('Ada file? ' . ($request->hasFile('file') ? 'YA' : 'TIDAK'));
    \Log::debug('File valid? ' . ($request->file('file')?->isValid() ? 'YA' : 'TIDAK'));
    \Log::debug('Error message: ' . ($request->file('file')?->getErrorMessage() ?? 'N/A'));

    $path = $file->store('songs', 'public');
    \Log::info('✅ Lagu disimpan di: ' . $path);

    Song::create([
        'title' => $request->title,
        'file_path' => $path,
    ]);

    return back()->with('success', 'Lagu berhasil diupload!');
}


    public function destroy(Song $song)
    {
        if ($song->file_path && Storage::disk('public')->exists($song->file_path)) {
            Storage::disk('public')->delete($song->file_path);
        }

        $song->delete();
        return back()->with('success', 'Lagu berhasil dihapus!');
    }
}
