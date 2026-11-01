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
    SongListController,
    LoveStoryController,
    KirimKadoController,
    InvitationImportController
};

// ========== ANTREAN REVIEW FORM UNDANGAN ==========
Route::get('/form-submissions', [\App\Http\Controllers\FormReviewController::class, 'index'])->name('form-review.index');
Route::post('/form-submissions/login', [\App\Http\Controllers\FormReviewController::class, 'login'])->middleware('throttle:5,1')->name('form-review.login');
Route::post('/form-submissions/logout', [\App\Http\Controllers\FormReviewController::class, 'logout'])->name('form-review.logout');
Route::get('/form-submissions/{submission}', [\App\Http\Controllers\FormReviewController::class, 'show'])->name('form-review.show');
Route::post('/form-submissions/{submission}/save', [\App\Http\Controllers\FormReviewController::class, 'save'])->name('form-review.save');
Route::post('/form-submissions/{submission}/publish', [\App\Http\Controllers\FormReviewController::class, 'publish'])->middleware('throttle:10,1')->name('form-review.publish');

// ========== SLUG MANAGEMENT ==========
Route::get('/', [SlugListController::class, 'index'])->name('slug.index');
Route::post('/slug-list', [SlugListController::class, 'store'])->name('slug.store');
Route::put('/slug-list/{id}', [SlugListController::class, 'update'])->name('slug.update');
Route::delete('/slug-list/{id}', [SlugListController::class, 'destroy'])->name('slug.destroy');

// ========== HERO & DETAIL UNDANGAN ==========
Route::get('/slug-list/{id}/edit', [HeroInvitationController::class, 'edit'])->name('slug.edit');
Route::post('/slug/{slug_id}/hero-invitation', [HeroInvitationController::class, 'store'])->name('hero.store');
Route::post('/hero-default/{picture}', [HeroInvitationController::class, 'storeDefaultPhoto'])->name('hero.default.store');

// ========== JSON IMPORT ==========
// JSON import only lives on the homepage and creates the invitation + slug.
Route::post('/import-json/preview', [InvitationImportController::class, 'previewCreate'])->name('import.create.preview');
Route::post('/import-json', [InvitationImportController::class, 'storeCreate'])->name('import.create.store');

// ========== ACARA ==========
Route::get('/slug/{slug_id}/acara', [AcaraController::class, 'edit'])->name('acara.edit');
Route::post('/slug/{slug_id}/acara', [AcaraController::class, 'store'])->name('acara.store');

// ========== COUNTING ==========
Route::get('/slug/{slug_id}/counting', [CountingController::class, 'edit'])->name('counting.edit');
Route::post('/slug/{slug_id}/counting', [CountingController::class, 'store'])->name('counting.store');

// ========== GALERI ==========
Route::post('/slug/{slug_id}/galleri', [GaleriController::class, 'store'])->name('galeri.store');
Route::delete('/slug/{slug_id}/galleri/photo', [GaleriController::class, 'destroyPhoto'])->name('galeri.photo.destroy');

// ========== MASTER BANK CMS ==========
Route::resource('/slug/banks', BankController::class);

// ========== LOVE GIFT ==========
Route::get('/slug/{slug_id}/lovegift', [LovegiftController::class, 'edit'])->name('lovegift.edit');
Route::post('/slug/{slug_id}/lovegift', [LovegiftController::class, 'store'])->name('lovegift.store');

// ========= KIRIM KADO ==========
Route::get('/slug/{slug_id}/kirim-kado', [KirimKadoController::class, 'edit'])->name('kirimkado.edit');
Route::post('/slug/{slug_id}/kirim-kado', [KirimKadoController::class, 'store'])->name('kirimkado.store');
Route::delete('/slug/{slug_id}/kirim-kado', [KirimKadoController::class, 'delete'])->name('kirimkado.delete');

// ========== LOVE STORY ==========
Route::get('/slug/{slug_id}/love-story', [LoveStoryController::class, 'edit'])->name('LoveStory.edit');
Route::post('/slug/{slug_id}/love-story', [LoveStoryController::class, 'store'])->name('LoveStory.store');
Route::delete('/slug/{slug_id}/love-story', [LoveStoryController::class, 'delete'])->name('LoveStory.delete');

// ========== SONG MANAGEMENT ==========
Route::resource('/slug/song', SongController::class);

// Song List (per slug)
Route::get('/slug/{slug_list_id}/song-list', [SongListController::class, 'index'])->name('songlist.index');
Route::post('/slug/{slug_list_id}/song-list', [SongListController::class, 'store'])->name('songlist.store');
Route::post('/slug/{slug_list_id}/song-list/upload', [SongListController::class, 'storeUploadedSong'])->name('songlist.upload');
Route::delete('/song-list/{songList}', [SongListController::class, 'destroy'])->name('songlist.destroy');
