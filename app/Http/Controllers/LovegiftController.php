<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lovegift;
use App\Models\SlugList;
use App\Models\Bank;

class LovegiftController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit($slug_id)
    {
        $slug = SlugList::findOrFail($slug_id);

        $banks = Bank::all();

        $love_gifts = Lovegift::with('bank')
            ->where('slug_list_id', $slug_id)
            ->get();

        return view(
            'slug.Lovegift',
            compact(
                'slug',
                'slug_id',
                'love_gifts',
                'banks'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request, $slug_id)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'bank_id' => 'required|array|max:3',
            'bank_id.*' => 'required|exists:banks,id',

            'no_rekening' => 'required|array',
            'no_rekening.*' => 'required|string|max:100',

            'pemilik_bank' => 'required|array',
            'pemilik_bank.*' => 'required|string|max:255',
        ]);


        /*
        |--------------------------------------------------------------------------
        | HAPUS DATA LAMA
        |--------------------------------------------------------------------------
        */

        Lovegift::where(
            'slug_list_id',
            $slug_id
        )->delete();


        /*
        |--------------------------------------------------------------------------
        | SIMPAN DATA BARU
        |--------------------------------------------------------------------------
        */

        foreach (
            $request->bank_id
            as $index => $bank_id
        ) {

            Lovegift::create([
                'slug_list_id' => $slug_id,

                'bank_id' => $bank_id,

                'no_rekening' =>
                    $request->no_rekening[$index],

                'pemilik_bank' =>
                    $request->pemilik_bank[$index],
            ]);
        }


        return back()->with(
            'success',
            'Love Gift berhasil disimpan!'
        );
    }
}