<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    SlugListController,
    HeroInvitationController,
    AcaraController,
    CountingController,
    GaleriController,
    LovegiftController,
    BankController,
    SongController,
    SongListController
    };

// ========== SLUG MANAGEMENT ==========
Route::get('/', [SlugListController::class, 'index'])->name('slug.index');
Route::post('/slug-list', [SlugListController::class, 'store'])->name('slug.store');
Route::put('/slug-list/{id}', [SlugListController::class, 'update'])->name('slug.update');
Route::delete('/slug-list/{id}', [SlugListController::class, 'destroy'])->name('slug.destroy');

// ========== HERO & DETAIL UNDANGAN ==========
Route::get('/slug-list/{id}/edit', [HeroInvitationController::class, 'edit'])->name('slug.edit');
Route::post('/slug/{slug_id}/hero-invitation', [HeroInvitationController::class, 'store'])->name('hero.store');

// ========== ACARA ==========
Route::get('/slug/{slug_id}/acara', [AcaraController::class, 'edit'])->name('acara.edit');
Route::post('/slug/{slug_id}/acara', [AcaraController::class, 'store'])->name('acara.store');

// ========== COUNTING ==========
Route::get('/slug/{slug_id}/counting', [CountingController::class, 'edit'])->name('counting.edit');
Route::post('/slug/{slug_id}/counting', [CountingController::class, 'store'])->name('counting.store');

// ========== GALERI ==========
Route::post('/slug/{slug_id}/galleri', [GaleriController::class, 'store'])->name('galeri.store');

// ========== MASTER BANK CMS ==========
Route::resource('/slug/banks', BankController::class);

// ========== LOVE GIFT ==========
Route::get('/slug/{slug_id}/lovegift', [LovegiftController::class, 'edit'])->name('lovegift.edit');
Route::post('/slug/{slug_id}/lovegift', [LovegiftController::class, 'store'])->name('lovegift.store');

// ========== SONG MANAGEMENT ==========
Route::resource('/slug/song', SongController::class);

// Song List (per slug)
Route::get('/slug/{slug_list_id}/song-list', [SongListController::class, 'index'])->name('songlist.index');
Route::post('/slug/{slug_list_id}/song-list', [SongListController::class, 'store'])->name('songlist.store');
Route::delete('/song-list/{songList}', [SongListController::class, 'destroy'])->name('songlist.destroy');



