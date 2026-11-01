<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FormSubmissionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // Hanya Form Undangan Laravel dengan shared token yang dapat mengirim antrean.
        $expected = (string) config('services.form_undangan.integration_token', '');
        $provided = (string) $request->header('X-CMS-Integration-Token', '');
        if (strlen($expected) < 32 || $provided === '' || !hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Akses integrasi ditolak.'], 401);
        }

        $data = $request->validate([
            'order_number' => ['required', 'string', 'regex:/^[A-Za-z0-9-]{6,80}$/'],
            'customer_whatsapp' => ['required', 'string', 'max:30'],
            'shopee_username' => ['nullable', 'string', 'max:120'],
            'display_order' => ['nullable', 'in:female_first,male_first'],
            'photo_package' => ['nullable', 'in:with_photo,no_photo'],
            'photo_count' => ['required', 'integer', 'between:0,10'],
            // Data tidak lengkap tetap boleh masuk antrean review.
            'payload' => ['required', 'array'],
            'payload.order_number' => ['required', 'string', 'max:80'],
            'payload.mempelai_1' => ['nullable', 'array'],
            'payload.mempelai_2' => ['nullable', 'array'],
            'payload.events' => ['nullable', 'array', 'max:3'],
            'payload.love_gifts' => ['nullable', 'array', 'max:3'],
            'payload.gift_delivery' => ['nullable', 'array'],
        ]);
        if ($data['payload']['order_number'] !== $data['order_number']) {
            throw ValidationException::withMessages(['order_number' => 'Nomor pesanan tidak cocok dengan isi JSON.']);
        }

        return DB::transaction(function () use ($data) {
            $submission = FormSubmission::where('order_number', $data['order_number'])
                ->lockForUpdate()->first();

            if ($submission && in_array($submission->status, ['published', 'in_review'], true)) {
                return response()->json([
                    'status' => 'review_locked',
                    'message' => 'Pesanan sedang ditinjau admin atau sudah dipublikasikan; pengiriman ulang tidak boleh menimpa data review.',
                ], 409);
            }

            if (!$submission) $submission = new FormSubmission(['order_number' => $data['order_number']]);
            $submission->fill([
                'customer_whatsapp' => $data['customer_whatsapp'],
                'shopee_username' => $data['shopee_username'] ?? null,
                'display_order' => $data['display_order'] ?? 'female_first',
                'photo_package' => $data['photo_package'] ?? null,
                'photo_count' => $data['photo_count'],
                'payload' => $data['payload'],
                'status' => 'pending_review',
            ])->save();

            return response()->json([
                'status' => 'pending_review',
                'submission_id' => $submission->id,
                'message' => 'Data diterima CMS untuk diperiksa, belum dipublikasikan.',
            ], 202);
        });
    }
}
