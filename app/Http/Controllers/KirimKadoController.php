<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KirimKado;
use App\Models\SlugList;

class KirimKadoController extends Controller
{
    public function edit($slug_id)
    {
        // Pastikan slug ada
        $slug = SlugList::findOrFail($slug_id);

        // Ambil data kirim kado untuk slug ini
        $kirimkado = KirimKado::where('slug_list_id', $slug_id)->first();

        return view('slug.KirimKado', compact('slug_id', 'kirimkado'));
    }

    public function store(Request $request, $slug_id)
    {
        // Validasi input
        $data = $request->validate([
            'nama_penerima'      => 'required|string',
            'no_hp_penerima'     => 'required|string',
            'alamat_penerima'  => 'required|string',
        ]);

        // Hapus data lama
        KirimKado::where('slug_list_id', $slug_id)->delete();

        // Simpan baru
        KirimKado::create([
            'slug_list_id'       => $slug_id,
            'nama_penerima'      => $data['nama_penerima'],
            'no_hp_penerima'     => $data['no_hp_penerima'],
            'alamat_penerima'  => $data['alamat_penerima'],
        ]);

        return redirect()->back()->with('success', 'Data Kirim Kado berhasil disimpan!');
    }

    public function delete($slug_id)
    {
        KirimKado::where('slug_list_id', $slug_id)->delete();
        return redirect()->back()->with('success', 'Data Kirim Kado berhasil dihapus!');
    }
}
