<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroInvitation;
use App\Models\SlugList;
use App\Models\Acara;
use App\Models\Galeri;
use App\Models\Lovegift;
use App\Models\Counting;
use App\Models\SongList;
use Illuminate\Http\JsonResponse;

class HeroInvitationApiController extends Controller
{
    /**
     * Ambil semua data undangan berdasarkan slug
     * Contoh: /api/slug/nandimia/listapi
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

            $heroInvitation = HeroInvitation::where('slug_id', $slugData->id)->first();
            $acaras = Acara::where('slug_list_id', $slugData->id)->get();
            $galeri = Galeri::where('slug_list_id', $slugData->id)->get();
            $lovegifts = Lovegift::with('bank')->where('slug_list_id', $slugData->id)->get();
            $counting = Counting::where('slug_list_id', $slugData->id)->first();
            $songlist = SongList::with('song')->where('slug_list_id', $slugData->id)->get();

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
                ]
            ];

            return response()->json($responseData, 200, [
                'Content-Type' => 'application/json; charset=utf-8',
                'Access-Control-Allow-Origin' => '*'
            ]);

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
