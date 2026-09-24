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
use Illuminate\Support\Facades\Storage;
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

    private const ALLOWED_TOP_LEVEL_KEYS = [
        'order_number',
        'theme',
        'mempelai_1',
        'mempelai_2',
        'events',
        'love_gifts',
        'gift_delivery',
    ];

    /**
     * Preview JSON for an existing invitation.
     */
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

    /**
     * Import JSON into an existing invitation.
     */
    public function store(Request $request, $slug_id)
    {
        $slug = SlugList::findOrFail($slug_id);
        $payload = $this->validatedPayload($request);
        $theme = $this->resolveTheme($payload, $request->input('theme_override'), true);
        $bankMap = $this->validateBanks($payload);

        DB::transaction(function () use ($slug, $payload, $theme, $bankMap) {
            $this->applyPayloadToSlug($slug, $payload, $theme, $bankMap);
        });

        return redirect()
            ->to(route('slug.edit', $slug->id) . '#json_import')
            ->with('success', 'Import JSON berhasil. Data undangan sudah masuk ke database dan tetap bisa diedit manual.');
    }

    /**
     * Preview homepage JSON before creating a new invitation.
     */
    public function previewCreate(Request $request)
    {
        $payload = $this->validatedPayload($request, true);
        $theme = $this->resolveTheme($payload, $request->input('theme_override'), false);
        $this->validateBanks($payload);

        [$invitationName, $slugPreview] = $this->deriveNewInvitationIdentity($payload);

        $preview = $this->buildPreview($payload, $theme);
        $preview['order_number'] = $payload['order_number'];
        $preview['invitation_name'] = $invitationName;
        $preview['slug'] = $slugPreview;

        return redirect()
            ->to(route('slug.index') . '#json-import-create')
            ->with('create_import_preview', $preview)
            ->with('create_import_json', $request->input('json_payload'))
            ->with('create_import_theme_override', $request->input('theme_override'));
    }

    /**
     * Create the slug and all supported invitation data in one transaction.
     */
    public function storeCreate(Request $request)
    {
        $payload = $this->validatedPayload($request, true);
        $theme = $this->resolveTheme($payload, $request->input('theme_override'), true);
        $bankMap = $this->validateBanks($payload);
        [$invitationName] = $this->deriveNewInvitationIdentity($payload);

        $slug = DB::transaction(function () use ($payload, $theme, $bankMap, $invitationName) {
            $slug = SlugList::create([
                'nama' => $invitationName,
                'keterangan' => $payload['order_number'],
                'theme' => $theme,
            ]);

            $this->applyPayloadToSlug($slug, $payload, $theme, $bankMap);

            return $slug;
        });

        return redirect()
            ->to(route('slug.edit', $slug->id) . '#hero')
            ->with(
                'success',
                "Undangan {$slug->nama} berhasil dibuat dari JSON. Slug: {$slug->slug}. Foto default dipakai otomatis jika tersedia."
            );
    }

    private function validatedPayload(Request $request, bool $creating = false): array
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

        $unknownKeys = array_values(array_diff(array_keys($payload), self::ALLOWED_TOP_LEVEL_KEYS));

        if ($unknownKeys) {
            throw ValidationException::withMessages([
                'json_payload' => 'Field JSON tidak dikenal: ' . implode(', ', $unknownKeys) . '.',
            ]);
        }

        if (!array_intersect(['mempelai_1', 'mempelai_2', 'events', 'love_gifts', 'gift_delivery'], array_keys($payload))) {
            throw ValidationException::withMessages([
                'json_payload' => 'JSON tidak berisi data undangan yang bisa diimpor.',
            ]);
        }

        $validator = Validator::make($payload, [
            'order_number' => $creating ? 'required|string|max:100' : 'sometimes|nullable|string|max:100',
            'theme' => 'nullable|string|max:50',

            'mempelai_1' => $creating ? 'required|array' : 'sometimes|nullable|array',
            'mempelai_1.full_name' => 'nullable|string|max:255',
            'mempelai_1.short_name' => 'nullable|string|max:255',
            'mempelai_1.parents' => 'nullable|string|max:255',

            'mempelai_2' => 'sometimes|nullable|array',
            'mempelai_2.full_name' => 'nullable|string|max:255',
            'mempelai_2.short_name' => 'nullable|string|max:255',
            'mempelai_2.parents' => 'nullable|string|max:255',

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
            'order_number.required' => 'Nomor pesanan wajib ada di JSON untuk membuat undangan baru.',
            'mempelai_1.required' => 'Mempelai 1 wajib ada untuk membuat undangan baru.',
        ]);

        $validator->after(function ($validator) use ($payload, $creating) {
            if (array_key_exists('mempelai_2', $payload)
                && $payload['mempelai_2'] !== null
                && !array_key_exists('mempelai_1', $payload)) {
                $validator->errors()->add('json_payload', 'Mempelai 2 tidak boleh ada tanpa Mempelai 1.');
            }

            if (!$creating) {
                return;
            }

            $mempelai1 = $payload['mempelai_1'] ?? null;
            if (!is_array($mempelai1) || blank($mempelai1['short_name'] ?? null)) {
                $validator->errors()->add('json_payload', 'Nama pendek Mempelai 1 wajib diisi karena dipakai untuk membuat nama dan slug.');
            }

            $mempelai2 = $payload['mempelai_2'] ?? null;
            if (is_array($mempelai2) && !blank($mempelai2) && blank($mempelai2['short_name'] ?? null)) {
                $validator->errors()->add('json_payload', 'Jika Mempelai 2 diisi, nama pendek Mempelai 2 wajib ada untuk membuat nama dan slug.');
            }
        });

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

    private function applyPayloadToSlug(SlugList $slug, array $payload, string $theme, array $bankMap): void
    {
        $slug->theme = $theme;

        if (array_key_exists('order_number', $payload) && filled($payload['order_number'])) {
            $slug->keterangan = trim($payload['order_number']);
        }

        $slug->save();

        if (array_key_exists('mempelai_1', $payload) || array_key_exists('mempelai_2', $payload)) {
            $hero = HeroInvitation::firstOrNew(['slug_id' => $slug->id]);

            // Kolom pria/wanita adalah nama legacy di database.
            // Importer memperlakukannya hanya sebagai slot urutan: Mempelai 1 lalu Mempelai 2.
            if (array_key_exists('mempelai_1', $payload)) {
                $this->applyPerson($hero, $payload['mempelai_1'], 'pria');
                $this->applyDefaultPhotoIfNeeded($hero, $payload['mempelai_1'], 'pria');
            }

            if (array_key_exists('mempelai_2', $payload)) {
                $this->applyPerson($hero, $payload['mempelai_2'], 'wanita');
                $this->applyDefaultPhotoIfNeeded($hero, $payload['mempelai_2'], 'wanita');
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

    private function applyDefaultPhotoIfNeeded(HeroInvitation $hero, ?array $person, string $type): void
    {
        if ($person === null) {
            return;
        }

        $column = $type === 'pria' ? 'foto_pria' : 'foto_wanita';

        if (filled($hero->{$column})) {
            return;
        }

        $defaultPath = $this->findDefaultPhoto($type);

        if ($defaultPath) {
            $hero->{$column} = $defaultPath;
        }
    }

    private function findDefaultPhoto(string $type): ?string
    {
        return collect(Storage::disk('public')->files('hero-defaults'))
            ->first(fn (string $path) => str_starts_with(basename($path), $type . '.'));
    }

    private function deriveNewInvitationIdentity(array $payload): array
    {
        $first = trim((string) data_get($payload, 'mempelai_1.short_name'));
        $second = trim((string) data_get($payload, 'mempelai_2.short_name'));
        $name = trim($first . ($second !== '' ? ' ' . $second : ''));

        $base = Str::slug($name);

        if ($base === '') {
            throw ValidationException::withMessages([
                'json_payload' => 'Nama pendek tidak bisa diubah menjadi slug yang valid.',
            ]);
        }

        $slug = $base;
        $i = 1;

        while (SlugList::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return [$name, $slug];
    }

    private function buildPreview(array $payload, ?string $theme): array
    {
        $preview = [
            'theme' => $theme,
            'mempelai_1' => $payload['mempelai_1'] ?? null,
            'mempelai_2' => $payload['mempelai_2'] ?? null,
            'events' => $payload['events'] ?? null,
            'love_gifts' => $payload['love_gifts'] ?? null,
        ];

        if (array_key_exists('gift_delivery', $payload)) {
            $preview['gift_delivery'] = $payload['gift_delivery'];
        }

        return $preview;
    }
}
