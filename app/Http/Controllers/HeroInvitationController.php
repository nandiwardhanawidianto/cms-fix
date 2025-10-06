<?php

namespace App\Http\Controllers;

use App\Models\HeroInvitation;
use App\Models\SlugList;
use Illuminate\Http\Request;
use App\Models\Acara;
use App\Models\Galeri;
use App\Models\Lovegift;
use App\Models\Bank;

class HeroInvitationController extends Controller
{
    public function edit($id)
    {
        $slug = SlugList::findOrFail($id);

        $heroInvitation = HeroInvitation::where('slug_id', $id)->first();

        // load acara biar partial acara ga error
        $acaras = Acara::where('slug_list_id', $id)->get();

        //load galeri
        $galeri = Galeri::where('slug_list_id', $id)->first();

        //load lovegift
        $lovegift = Lovegift::where('slug_list_id', $id)->first();

        //load masterbankcms
        $banks = Bank::all();

        return view('slug.edit', [
            'slug' => $slug,
            'slug_id' => $id, // ✅ ini yang hilang
            'heroInvitation' => $heroInvitation,
            'acaras' => $acaras,
            'galeri' => $galeri,
            'lovegift' => $lovegift,
            'banks' =>$banks,
        ]);
    }


    public function store(Request $request, $slug_id)
    {
        $data = $request->validate([
            'nama_panggilan_pria' => 'required|string|max:255',
            'nama_lengkap_pria'   => 'required|string|max:255',
            'foto_pria'           => 'nullable|image|max:2048',
            'orangtua_pria'       => 'required|string|max:255',

            'nama_panggilan_wanita' => 'required|string|max:255',
            'nama_lengkap_wanita'   => 'required|string|max:255',
            'foto_wanita'           => 'nullable|image|max:2048',
            'orangtua_wanita'       => 'required|string|max:255',
        ]);

        if ($request->hasFile('foto_pria')) {
            $data['foto_pria'] = $request->file('foto_pria')->store('hero', 'public');
        }
        if ($request->hasFile('foto_wanita')) {
            $data['foto_wanita'] = $request->file('foto_wanita')->store('hero', 'public');
        }

        $data['slug_id'] = $slug_id;

        HeroInvitation::updateOrCreate(['slug_id' => $slug_id], $data);

        return back()->with('success', 'Hero & Invitation berhasil disimpan!');
    }
}
