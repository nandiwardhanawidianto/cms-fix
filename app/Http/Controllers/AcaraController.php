<?php

namespace App\Http\Controllers;

use App\Models\Acara;
use App\Models\SlugList;
use App\Models\HeroInvitation;
use Illuminate\Http\Request;

class AcaraController extends Controller
{
    public function edit($slug_id)
    {
        // Ambil slug
        $slug = SlugList::findOrFail($slug_id);

        // Ambil hero invitation
        $heroInvitation = HeroInvitation::where('slug_id', $slug_id)->first();

        // Ambil acara-acara
        $acaras = Acara::where('slug_list_id', $slug_id)->get();

        return view('slug.acara', [
            'slug_id'        => $slug_id, 
            'slug'           => $slug,
            'heroInvitation' => $heroInvitation,
            'acaras'         => $acaras,
        ]);
    }

    public function store(Request $request, $slug_id)
    {
        $validated = $request->validate([
            'nama_acara.*'    => 'required|string|max:255',
            'tanggal_acara.*' => 'required|date',
            'pukul_acara.*'   => 'required|string|max:50',
            'alamat_acara.*'  => 'required|string',
            'link_acara.*'    => 'required|string',
        ]);

        // Hapus data lama
        Acara::where('slug_list_id', $slug_id)->delete();

        // Simpan data baru
        foreach ($request->nama_acara as $i => $nama) {
            Acara::create([
                'slug_list_id' => $slug_id,
                'nama_acara'   => $nama,
                'tanggal_acara'=> $request->tanggal_acara[$i],
                'pukul_acara'  => $request->pukul_acara[$i],
                'alamat_acara' => $request->alamat_acara[$i],
                'link_acara'   => $request->link_acara[$i],
            ]);
        }

        return back()->with('success', 'Acara berhasil disimpan!');
    }
}
