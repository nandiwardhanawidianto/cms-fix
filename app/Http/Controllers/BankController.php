<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bank;
use Illuminate\Support\Facades\Storage;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::all();
        return view('/slug/mastercms', compact('banks'));
    }

    public function create()
    {
        return view('banks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:255',
            'logo_bank' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        $logoPath = null;
        if ($request->hasFile('logo_bank')) {
            $logoPath = $request->file('logo_bank')->store('bank_logos', 'public');
        }

        Bank::create([
            'nama_bank' => $request->nama_bank,
            'logo_bank' => $logoPath
        ]);

        return redirect()->route('banks.index')->with('success', 'Bank berhasil ditambahkan');
    }

    public function destroy(Bank $bank)
    {
        if ($bank->logo_bank) {
            Storage::disk('public')->delete($bank->logo_bank);
        }

        $bank->delete();

        return redirect()->route('banks.index')->with('success', 'Bank berhasil dihapus');
    }
}
