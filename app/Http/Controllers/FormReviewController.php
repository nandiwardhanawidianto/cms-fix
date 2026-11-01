<?php

namespace App\Http\Controllers;

use App\Models\FormSubmission;
use App\Models\SlugList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FormReviewController extends Controller
{
    private const THEMES = ['violet', 'sage', 'brown', 'jawa', 'bali', 'pink', 'biru', 'hitam', 'dayak', 'bugis'];

    private function guard(Request $request): void
    {
        abort_unless(
            strlen((string) config('services.form_undangan.review_password', '')) >= 12
            && $request->session()->get('cms_review_authorized') === true,
            403,
            'Silakan login ke Review Form Undangan terlebih dahulu.'
        );
    }

    public function index(Request $request)
    {
        if (!$request->session()->get('cms_review_authorized') ||
            strlen((string) config('services.form_undangan.review_password', '')) < 12) {
            return view('form-review.login');
        }

        return view('form-review.index', [
            'submissions' => FormSubmission::orderByRaw("CASE WHEN status = 'pending_review' THEN 0 ELSE 1 END")
                ->orderByDesc('updated_at')->paginate(25),
            'pendingCount' => FormSubmission::where('status', 'pending_review')->count(),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate(['password' => ['required', 'string']]);
        $expected = (string) config('services.form_undangan.review_password', '');
        if (strlen($expected) < 12 || !hash_equals($expected, (string) $request->input('password'))) {
            throw ValidationException::withMessages(['password' => 'Kata sandi salah atau belum disetel di server.']);
        }

        $request->session()->regenerate();
        $request->session()->put('cms_review_authorized', true);
        return redirect()->route('form-review.index');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('cms_review_authorized');
        $request->session()->regenerateToken();
        return redirect()->route('form-review.index');
    }

    public function show(Request $request, FormSubmission $submission)
    {
        $this->guard($request);
        $payload = $submission->payload ?? [];
        $suggestedName = trim((string) data_get($payload, 'mempelai_1.short_name').' '.(string) data_get($payload, 'mempelai_2.short_name'));
        return view('form-review.show', [
            'submission' => $submission,
            'themes' => self::THEMES,
            'suggestedName' => $suggestedName,
            'suggestedSlug' => Str::slug($suggestedName),
        ]);
    }

    private function parseReview(Request $request, FormSubmission $submission): array
    {
        $data = $request->validate([
            'json_payload' => ['required', 'string'],
            'invitation_name' => ['required', 'string', 'max:255'],
            'invitation_slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'theme' => ['required', 'in:'.implode(',', self::THEMES)],
        ]);
        try {
            $payload = json_decode($data['json_payload'], true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw ValidationException::withMessages(['json_payload' => 'JSON tidak valid.']);
        }
        if (!is_array($payload) || ($payload['order_number'] ?? null) !== $submission->order_number) {
            throw ValidationException::withMessages(['json_payload' => 'Nomor pesanan dalam JSON tidak boleh berubah.']);
        }
        return [$data, $payload];
    }

    public function save(Request $request, FormSubmission $submission)
    {
        $this->guard($request);
        abort_unless($submission->status === 'pending_review', 409);
        [$data, $payload] = $this->parseReview($request, $submission);
        $submission->update(['payload' => $payload]);
        return redirect()->route('form-review.show', $submission)->with('success', 'Perubahan JSON disimpan. Undangan belum diterbitkan.');
    }

    public function publish(Request $request, FormSubmission $submission, InvitationImportController $importer)
    {
        $this->guard($request);
        $request->validate(['confirmed' => ['accepted']]);
        [$data, $payload] = $this->parseReview($request, $submission);

        $slug = DB::transaction(function () use ($submission, $data, $payload, $importer) {
            $locked = FormSubmission::whereKey($submission->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'pending_review', 409, 'Pesanan sudah dipublikasikan.');
            $slug = $importer->publishReviewed(
                $payload,
                $data['theme'],
                $data['invitation_name'],
                $data['invitation_slug'] ?? null,
                $locked->display_order
            );
            $locked->update([
                'payload' => $payload,
                'status' => 'published',
                'published_slug_id' => $slug->id,
                'published_at' => now(),
            ]);
            return $slug;
        });

        return redirect()->route('slug.edit', $slug->id)
            ->with('success', "Undangan diterbitkan dengan slug {$slug->slug}. Periksa foto dan pengaturan publikasi.");
    }
}
