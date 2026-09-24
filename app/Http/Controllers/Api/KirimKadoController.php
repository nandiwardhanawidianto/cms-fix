<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KirimKado;
use App\Models\SlugList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KirimKadoController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        $slugData = SlugList::where('slug', $slug)->first();

        if (!$slugData) {
            return response()->json([
                'success' => false,
                'message' => 'Slug tidak ditemukan.',
            ], 404);
        }

        $gift = KirimKado::where('slug_list_id', $slugData->id)->first();

        return response()->json([
            'success' => true,
            'data' => $gift ? $this->formatGift($gift) : null,
        ]);
    }

    public function store(Request $request, string $slug): JsonResponse
    {
        $slugData = SlugList::where('slug', $slug)->first();

        if (!$slugData) {
            return response()->json([
                'success' => false,
                'message' => 'Slug tidak ditemukan.',
            ], 404);
        }

        if (!$request->filled('no_hp_penerima') && $request->filled('no_hp')) {
            $request->merge([
                'no_hp_penerima' => $request->input('no_hp'),
            ]);
        }

        $data = $request->validate([
            'nama_penerima' => 'required|string|max:255',
            'no_hp_penerima' => 'required|string|max:50',
            'alamat_penerima' => 'required|string',
        ]);

        $gift = KirimKado::updateOrCreate(
            ['slug_list_id' => $slugData->id],
            [
                'nama_penerima' => $data['nama_penerima'],
                'no_hp_penerima' => $data['no_hp_penerima'],
                'alamat_penerima' => $data['alamat_penerima'],
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $this->formatGift($gift),
            'message' => 'Data kirim kado berhasil disimpan.',
        ]);
    }

    public function destroy(string $slug): JsonResponse
    {
        $slugData = SlugList::where('slug', $slug)->first();

        if (!$slugData) {
            return response()->json([
                'success' => false,
                'message' => 'Slug tidak ditemukan.',
            ], 404);
        }

        KirimKado::where('slug_list_id', $slugData->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kirim kado berhasil dihapus.',
        ]);
    }

    private function formatGift(KirimKado $gift): array
    {
        return [
            'id' => $gift->id,
            'slug_list_id' => $gift->slug_list_id,
            'nama_penerima' => $gift->nama_penerima,
            'no_hp' => $gift->no_hp_penerima,
            'no_hp_penerima' => $gift->no_hp_penerima,
            'alamat_penerima' => $gift->alamat_penerima,
            'created_at' => $gift->created_at?->toISOString(),
            'updated_at' => $gift->updated_at?->toISOString(),
        ];
    }
}
