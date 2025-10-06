<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lovegift;
use App\Models\SlugList;
use App\Models\HeroInvitation;
use App\Models\Bank;

class LovegiftController extends Controller
{
    public function edit($slug_id)
    {
        // Pastikan slug ada
        $slug = SlugList::findOrFail($slug_id);

        // Ambil semua bank master CMS
        $banks = Bank::all();

        // Ambil data Lovegift berdasarkan slug
        $love_gifts = Lovegift::where('slug_list_id', $slug_id)
            ->with('bank')
            ->get();

        // Debug kalau kosong
        // dd($love_gifts, $banks);

        return view('slug.Lovegift', compact('slug_id', 'love_gifts', 'banks'));
    }

    public function store(Request $request, $slug_id)
    {
        // Hapus semua lovegift lama
        Lovegift::where('slug_list_id', $slug_id)->delete();

        $bank_ids = $request->bank_id ?? [];
        $no_rekenings = $request->no_rekening ?? [];
        $pemilik_banks = $request->pemilik_bank ?? [];

        foreach ($bank_ids as $index => $bank_id) {
            Lovegift::create([
                'slug_list_id' => $slug_id,
                'bank_id' => $bank_id,
                'no_rekening' => $no_rekenings[$index] ?? '',
                'pemilik_bank' => $pemilik_banks[$index] ?? '',
            ]);
        }

        return redirect()->back()->with('success', 'Love Gift berhasil disimpan!');
    }
}
