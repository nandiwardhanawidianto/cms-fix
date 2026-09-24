<?php

namespace App\Http\Controllers;

use App\Models\Acara;
use App\Models\Bank;
use App\Models\HeroInvitation;
use App\Models\KirimKado;
use App\Models\Lovegift;
use App\Models\SlugList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use JsonException;

class InvitationImportController extends Controller
{
    private const THEMES = [
        'violet',
        'sage',
        'brown',
        'jawa',
        'bali',
        'pink',
        'biru',
        'hitam',
        'dayak',
        'bugis',
    ];

    public function preview(Request $request, $slug_id)
    {
        $slug = SlugList::findOrFail($slug_id);
        $payload = $this->validatedPayload($request);
        $theme = $this->resolveTheme($payload, $request->input('theme_override'), false);
        $this->validateBanks($payload);

        return redirect()
            ->to(route('slug.edit', $slug->id) . '#json_import')
            ->with('import_preview', $this->buildPreview($payload, $theme))
            ->with('import_json', $request->input('json_payload'))
            ->with('import_theme_override', $request->input('theme_override'));
    }

    public function store(Request $request, $slug_id)
    {
        $slug = SlugList::findOrFail($slug_id);
        $payload = $this->validatedPayload($request);
        $theme = $this->resolveTheme($payload, $request->input('theme_override'), true);
        $bankMap = $this->validateBanks($payload);

        DB::transaction(function () use ($slug, $payload, $theme, $bankMap) {
            $slug->theme = $theme;
            $slug->save();

            if (array_key_exists('groom', $payload) || array_key_exists('bride', $payload)) {
                $hero = HeroInvitation::firstOrNew(['slug_id' => $slug->id]);

                if (array_key_exists('groom', $payload)) {
                    $this->applyPerson($hero, $payload['groom'], 'pria');
                }

                if (array_key_exists('bride', $payload)) {
                    $this->applyPerson($hero, $payload['bride'], 'wanita');
                }

                $hero->slug_id = $slug->id;
                $hero->save();
            }

            if (array_key_exists('events', $payload)) {
                Acara::where('slug_list_id', $slug->id)->delete();

                foreach ($payload['events'] as $event) {
                    Acara::create([
                        'slug_list_id' => $slug->id,
                        'nama_acara' => $event['name'],
                        'tanggal_acara' => $event['date'],
                        'pukul_acara' => $event['time'],
                        'alamat_acara' => $event['address'],
                        'link_acara' => $event['maps'] ?? null,
                    ]);
                }
            }

            if (array_key_exists('love_gifts', $payload)) {
                Lovegift::where('slug_list_id', $slug->id)->delete();

                foreach ($payload['love_gifts'] as $index => $gift) {
                    Lovegift::create([
                        'slug_list_id' => $slug->id,
                        'bank_id' => $bankMap[$index]->id,
                        'no_rekening' => $gift['account_number'],
                        'pemilik_bank' => $gift['account_name'],
                    ]);
                }
            }

            if (array_key_exists('gift_delivery', $payload)) {
                KirimKado::where('slug_list_id', $slug->id)->delete();

                if ($payload['gift_delivery'] !== null) {
                    KirimKado::create([
                        'slug_list_id' => $slug->id,
                        'nama_penerima' => $payload['gift_delivery']['recipient_name'],
                        'no_hp_penerima' => $payload['gift_delivery']['phone'],
                        'alamat_penerima' => $payload['gift_delivery']['address'],
                    ]);
                }
            }
        });

        return redirect()
            ->to(route('slug.edit', $slug->id) . '#json_import')
            ->with('success', 'Import JSON berhasil. Data undangan sudah masuk ke database dan tetap bisa diedit manual.');
    }

    private function validatedPayload(Request $request): array
    {
        $request->validate([
            'json_payload' => 'required|string',
            'theme_override' => 'nullable|string',
        ]);

        try {
            $payload = json_decode($request->input('json_payload'), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw ValidationException::withMessages([
                'json_payload' => 'JSON tidak valid: ' . $e->getMessage(),
            ]);
        }

        if (!is_array($payload)) {
            throw ValidationException::withMessages([
                'json_payload' => 'JSON harus berupa object.',
            ]);
        }

        if (!array_intersect(['groom', 'bride', 'events', 'love_gifts', 'gift_delivery'], array_keys($payload))) {
            throw ValidationException::withMessages([
                'json_payload' => 'JSON tidak berisi data undangan yang bisa diimpor.',
            ]);
        }

        $validator = Validator::make($payload, [
            'theme' => 'nullable|string|max:50',

            'groom' => 'sometimes|nullable|array',
            'groom.full_name' => 'nullable|string|max:255',
            'groom.short_name' => 'nullable|string|max:255',
            'groom.parents' => 'nullable|string|max:255',

            'bride' => 'sometimes|nullable|array',
            'bride.full_name' => 'nullable|string|max:255',
            'bride.short_name' => 'nullable|string|max:255',
            'bride.parents' => 'nullable|string|max:255',

            'events' => 'sometimes|array|max:3',
            'events.*.name' => 'required|string|max:255',
            'events.*.date' => 'required|date_format:Y-m-d',
            'events.*.time' => 'required|string|max:50',
            'events.*.address' => 'required|string',
            'events.*.maps' => 'nullable|string|max:2000',

            'love_gifts' => 'sometimes|array|max:3',
            'love_gifts.*.bank' => 'required|string|max:100',
            'love_gifts.*.account_number' => 'required|string|max:100',
            'love_gifts.*.account_name' => 'required|string|max:255',

            'gift_delivery' => 'sometimes|nullable|array',
            'gift_delivery.recipient_name' => 'required_with:gift_delivery|string|max:255',
            'gift_delivery.phone' => 'required_with:gift_delivery|string|max:50',
            'gift_delivery.address' => 'required_with:gift_delivery|string',
        ], [
            'events.*.date.date_format' => 'Tanggal acara wajib memakai format YYYY-MM-DD, contoh 2026-10-01.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    private function resolveTheme(array $payload, ?string $override, bool $required): ?string
    {
        $candidate = filled($override)
            ? $override
            : ($payload['theme'] ?? null);

        if (!filled($candidate)) {
            if ($required) {
                throw ValidationException::withMessages([
                    'theme_override' => 'Theme belum ditentukan. Pilih theme sebelum import.',
                ]);
            }

            return null;
        }

        $theme = Str::lower(trim($candidate));

        if (!in_array($theme, self::THEMES, true)) {
            throw ValidationException::withMessages([
                'theme_override' => 'Theme tidak dikenal. Pilih salah satu theme yang tersedia di CMS.',
            ]);
        }

        return $theme;
    }

    private function validateBanks(array $payload): array
    {
        if (!array_key_exists('love_gifts', $payload)) {
            return [];
        }

        $banks = Bank::all();
        $bankMap = [];

        foreach ($payload['love_gifts'] as $index => $gift) {
            $wanted = Str::lower(trim($gift['bank']));

            $bank = $banks->first(function (Bank $bank) use ($wanted) {
                return Str::lower(trim($bank->nama_bank)) === $wanted;
            });

            if (!$bank) {
                throw ValidationException::withMessages([
                    'json_payload' => "Bank '{$gift['bank']}' belum ada di Master Bank. Tambahkan bank itu dulu atau perbaiki nama bank di JSON.",
                ]);
            }

            $bankMap[$index] = $bank;
        }

        return $bankMap;
    }

    private function applyPerson(HeroInvitation $hero, ?array $person, string $suffix): void
    {
        if ($person === null) {
            $hero->{'nama_lengkap_' . $suffix} = null;
            $hero->{'nama_panggilan_' . $suffix} = null;
            $hero->{'orangtua_' . $suffix} = null;
            return;
        }

        $mapping = [
            'full_name' => 'nama_lengkap_' . $suffix,
            'short_name' => 'nama_panggilan_' . $suffix,
            'parents' => 'orangtua_' . $suffix,
        ];

        foreach ($mapping as $jsonKey => $modelField) {
            if (array_key_exists($jsonKey, $person)) {
                $hero->{$modelField} = $person[$jsonKey];
            }
        }
    }

    private function buildPreview(array $payload, ?string $theme): array
    {
        $preview = [
            'theme' => $theme,
            'groom' => $payload['groom'] ?? null,
            'bride' => $payload['bride'] ?? null,
            'events' => $payload['events'] ?? null,
            'love_gifts' => $payload['love_gifts'] ?? null,
        ];

        if (array_key_exists('gift_delivery', $payload)) {
            $preview['gift_delivery'] = $payload['gift_delivery'];
        }

        return $preview;
    }
}
