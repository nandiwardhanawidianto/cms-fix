<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GuestMessage;
use App\Models\SlugList;

class GuestMessageController extends Controller
{
    /**
     * Ambil semua ucapan berdasarkan slug (bukan ID)
     * contoh: GET /api/guest-messages/nandimia
     */
    public function index($slug)
    {
        $slugData = SlugList::where('slug', $slug)->first();

        if (!$slugData) {
            return response()->json([
                'success' => false,
                'message' => 'Slug tidak ditemukan'
            ], 404);
        }

        // ✅ pakai slug_list_id, bukan slug_id
        $messages = GuestMessage::where('slug_list_id', $slugData->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data ucapan berhasil diambil',
            'data' => $messages
        ]);
    }

    /**
     * Simpan ucapan baru berdasarkan slug unik
     * contoh: POST /api/guest-messages  (body: { slug_id: "nandimia", ... })
     */
    public function store(Request $request, $slug)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'attendance' => 'required|string|max:50',
                'message' => 'required|string|max:500',
            ]);

            $slugData = SlugList::where('slug', $slug)->firstOrFail();

            $message = GuestMessage::create([
                'slug_list_id' => $slugData->id,
                'name' => $validated['name'],
                'attendance' => $validated['attendance'],
                'message' => $validated['message'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ucapan berhasil dikirim!',
                'data' => $message
            ]);

        } catch (\Throwable $e) {
            // Tangkap semua error supaya bisa kelihatan
            return response()->json([
                'success' => false,
                'message' => 'Terjadi error: '.$e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
}
}
