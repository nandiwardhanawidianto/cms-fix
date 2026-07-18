<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\love_story;
use App\Models\KirimKado;
use App\Models\HeroInvitation;
use App\Models\SlugList;
use App\Models\Acara;
use App\Models\Galeri;
use App\Models\Lovegift;
use App\Models\Counting;
use App\Models\SongList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HeroInvitationApiController extends Controller
{
    /**
     * Simpan slug baru
     */
    public function saveslug(Request $request)
    {
        try {
            $data = $request->all();

            $slug = SlugList::create([
                'slug' => $data['slug'],
                'nama' => $data['nama'],
                'keterangan' => $data['slug_keterangan'] ?? null,
                'theme' => $data['theme'] ?? null,
            ]);
   
            return response()->json([
                'success' => true,
                'slug_id' => $slug->id,
                'slug' => $slug->slug,
                'message' => 'Slug berhasil disimpan!',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Simpan data undangan
     */
    public function saveInvitation(Request $request)
    {
        try {
            $data = $request->all();
            $slug = SlugList::find($data['slug_id']);

            if (!$slug) {
                return response()->json(['error' => 'Slug tidak ditemukan'], 404);
            }

            // Simpan hero_invitations
            $hero = HeroInvitation::create([
                'slug_id' => $slug->id,
                'nama_lengkap_pria' => $data['pengantin_pria']['nama_lengkap'] ?? null,
                'nama_panggilan_pria' => $data['pengantin_pria']['nama_pendek'] ?? null,
                'orangtua_pria' => $data['pengantin_pria']['ortu'] ?? null,
                'nama_lengkap_wanita' => $data['pengantin_wanita']['nama_lengkap'] ?? null,
                'nama_panggilan_wanita' => $data['pengantin_wanita']['nama_pendek'] ?? null,
                'orangtua_wanita' => $data['pengantin_wanita']['ortu'] ?? null,
            ]);

            // Simpan acara
            if (isset($data['acaras'])) {
                foreach ($data['acaras'] as $acara) {
                    Acara::create([
                        'slug_list_id' => $slug->id,
                        'nama_acara' => $acara['nama_acara'] ?? null,
                        'tanggal_acara' => $acara['tanggal'] ?? null,
                        'pukul_acara' => $acara['jam'] ?? null,
                        'alamat_acara' => $acara['tempat'] ?? null,
                        'link_acara' => $acara['maps'] ?? null,
                    ]);
                }
            }

            // Simpan lovegift
            if (isset($data['lovegift'])) {
                Lovegift::create([
                    'slug_list_id' => $slug->id,
                    'bank_id' => $data['lovegift']['bank_id'] ?? null,
                    'bank_name' => $data['lovegift']['bank_name'] ?? null,
                    'no_rekening' => $data['lovegift']['no_rekening'] ?? null,
                    'pemilik_bank' => $data['lovegift']['nama_rekening'] ?? null,
                ]);
            }

            return response()->json([
                'success' => true,
                'slug_id' => $slug->id,
                'slug' => $slug->slug,
                'message' => 'Data undangan berhasil disimpan!',
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API utama list data undangan berdasarkan slug
     */
    public function listapi($slug): JsonResponse
    {
        try {
            $slugData = SlugList::where('slug', $slug)->first();

            if (!$slugData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Slug tidak ditemukan'
                ], 404);
            }

            // Ambil semua data
            $heroInvitation = HeroInvitation::where('slug_id', $slugData->id)->first();
            $acaras = Acara::where('slug_list_id', $slugData->id)->get();
            $galeri = Galeri::where('slug_list_id', $slugData->id)->get();
            $lovegifts = Lovegift::with('bank')->where('slug_list_id', $slugData->id)->get();
            $counting = Counting::where('slug_list_id', $slugData->id)->first();
            $songlist = SongList::with('song')->where('slug_list_id', $slugData->id)->get();
            $lovestory = love_story::where('slug_list_id', $slugData->id)->first();
            $kirimKado = KirimKado::where('slug_list_id', $slugData->id)->first();

            // Response JSON
            $responseData = [
                'success' => true,
                'data' => [
                    'slug' => $this->formatSlugData($slugData),
                    'heroInvitation' => $heroInvitation ? $this->formatHeroInvitation($heroInvitation) : null,
                    'counting' => $counting ? $this->formatCounting($counting) : null,
                    'acaras' => $this->formatAcaras($acaras),
                    'galeri' => $this->formatGaleri($galeri),
                    'lovegift' => $this->formatLovegifts($lovegifts),
                    'songlist' => $this->formatSonglist($songlist),
                    'kirimKado' => $kirimKado ? $this->formatKirimKado($kirimKado) : null,
                    'lovestory' => $lovestory ? $this->formatLoveStory($lovestory) : null,
                    ]
            ];

            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            \Log::error('API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    private function formatSlugData($slugData): array
    {
        return [
            'id' => $slugData->id,
            'slug' => $slugData->slug,
            'keterangan' => $slugData->keterangan,
            'theme' => $slugData->theme,
            'created_at' => $slugData->created_at?->toISOString(),
            'updated_at' => $slugData->updated_at?->toISOString(),
        ];
    }

    private function formatHeroInvitation($hero): array
    {
        return [
            'id' => $hero->id,
            'slug_id' => $hero->slug_id,
            'nama_lengkap_pria' => $hero->nama_lengkap_pria,
            'nama_lengkap_wanita' => $hero->nama_lengkap_wanita,
            'nama_panggilan_pria' => $hero->nama_panggilan_pria,
            'nama_panggilan_wanita' => $hero->nama_panggilan_wanita,
            'foto_pria' => $hero->foto_pria ? asset('storage/' . $hero->foto_pria) : null,
            'foto_wanita' => $hero->foto_wanita ? asset('storage/' . $hero->foto_wanita) : null,
            'orangtua_pria' => $hero->orangtua_pria,
            'orangtua_wanita' => $hero->orangtua_wanita,
            'created_at' => $hero->created_at?->toISOString(),
            'updated_at' => $hero->updated_at?->toISOString(),
        ];
    }

    private function formatCounting($counting): array
    {
        return [
            'id' => $counting->id,
            'slug_list_id' => $counting->slug_list_id,
            'nama_surat' => $counting->nama_surat,
            'deskripsi_surat' => $counting->deskripsi_surat,
            'surat_arab' => $counting->surat_arab,
            'created_at' => $counting->created_at?->toISOString(),
            'updated_at' => $counting->updated_at?->toISOString(),
        ];
    }

    private function formatAcaras($acaras): array
    {
        return $acaras->map(function ($acara) {
            return [
                'id' => $acara->id,
                'slug_list_id' => $acara->slug_list_id,
                'nama_acara' => $acara->nama_acara,
                'tanggal_acara' => $acara->tanggal_acara,
                'pukul_acara' => $acara->pukul_acara,
                'alamat_acara' => $acara->alamat_acara,
                'link_acara' => $acara->link_acara,
                'created_at' => $acara->created_at?->toISOString(),
                'updated_at' => $acara->updated_at?->toISOString(),
            ];
        })->toArray();
    }

    private function formatGaleri($galeri): array
    {
        return $galeri->map(function ($g) {
            $carouselAtas = [];
            $carouselBawah = [];

            try {
                if ($g->carousel_atas && $this->isJson($g->carousel_atas)) {
                    $parsedAtas = json_decode($g->carousel_atas, true);
                    if (is_array($parsedAtas)) {
                        $carouselAtas = array_map(fn($p) => url('storage/' . str_replace('\\', '/', $p)), $parsedAtas);
                    }
                }

                if ($g->carousel_bawah && $this->isJson($g->carousel_bawah)) {
                    $parsedBawah = json_decode($g->carousel_bawah, true);
                    if (is_array($parsedBawah)) {
                        $carouselBawah = array_map(fn($p) => url('storage/' . str_replace('\\', '/', $p)), $parsedBawah);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error parsing galeri data: ' . $e->getMessage());
            }

            return [
                'id' => $g->id,
                'slug_list_id' => $g->slug_list_id,
                'carousel_atas' => $carouselAtas,
                'carousel_bawah' => $carouselBawah,
                'created_at' => $g->created_at?->toISOString(),
                'updated_at' => $g->updated_at?->toISOString(),
            ];
        })->toArray();
    }

    private function formatLovegifts($lovegifts): array
    {
        return $lovegifts->map(function ($lg) {
            $bank = $lg->bank;
            return [
                'id' => $lg->id,
                'bank_id' => $bank?->id,
                'bank_name' => $bank?->nama_bank,
                'bank_logo' => $bank && $bank->logo_bank ? asset('storage/' . $bank->logo_bank) : null,
                'no_rekening' => $lg->no_rekening,
                'pemilik_bank' => $lg->pemilik_bank,
                'created_at' => $lg->created_at?->toISOString(),
                'updated_at' => $lg->updated_at?->toISOString(),
            ];
        })->toArray();
    }

    private function formatLoveStory($lovestory): array
    {
        return [
            'slug_list_id' => $lovestory->slug_list_id,
            'judul_awal_pertemuan' => $lovestory->judul_awal_pertemuan,
            'judul_menjalin_hubungan' => $lovestory->judul_menjalin_hubungan,
            'judul_lamaran' => $lovestory->judul_lamaran,
            'awal_pertemuan' => $lovestory->awal_pertemuan,
            'menjalin_hubungan' => $lovestory->menjalin_hubungan,
            'lamaran' => $lovestory->lamaran,
            'gambar_awal' => $lovestory->gambar_awal ? asset('storage/' . $lovestory->gambar_awal) : null,
            'gambar_hubungan' => $lovestory->gambar_hubungan ? asset('storage/' . $lovestory->gambar_hubungan) : null,
            'gambar_lamaran' => $lovestory->gambar_lamaran ? asset('storage/' . $lovestory->gambar_lamaran) : null,
            'created_at' => $lovestory->created_at?->toISOString(),
            'updated_at' => $lovestory->updated_at?->toISOString(),
        ];
    }

    private function formatKirimKado($data): array
    {
        return [
            'id' => $data->id,
            'slug_list_id' => $data->slug_list_id,
            'nama_penerima' => $data->nama_penerima,
            'no_hp' => $data->no_hp_penerima,
            'alamat_penerima' => $data->alamat_penerima,
            'created_at' => $data->created_at?->toISOString(),
            'updated_at' => $data->updated_at?->toISOString(),
        ];
    }

    private function formatSonglist($songlist): array
    {
        return $songlist->map(function ($item) {
            $song = $item->song;
            return [
                'id' => $item->id,
                'slug_list_id' => $item->slug_list_id,
                'song' => $song ? [
                    'id' => $song->id,
                    'title' => $song->title,
                    'file_path' => $song->file_path,
                    'url' => $song->url,
                ] : null,
                'created_at' => $item->created_at?->toISOString(),
                'updated_at' => $item->updated_at?->toISOString(),
            ];
        })->toArray();
    }



    private function isJson($string): bool
    {
        if (!is_string($string)) return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
