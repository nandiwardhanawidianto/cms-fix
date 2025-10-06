<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HeroInvitationApiController;
use App\Http\Controllers\Api\GuestMessageController;


Route::options('/guest-messages', function () {
    return response()->json([], 200)
        ->header('Access-Control-Allow-Origin', 'http://localhost:5173')
        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept')
        ->header('Access-Control-Allow-Credentials', 'true')
        ->header('Access-Control-Max-Age', '86400');
});

Route::options('/guest-messages/{any}', function () {
    return response()->json([], 200)
        ->header('Access-Control-Allow-Origin', 'http://localhost:5173')
        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept')
        ->header('Access-Control-Allow-Credentials', 'true')
        ->header('Access-Control-Max-Age', '86400');
})->where('any', '.*');


// API Routes
Route::get('/slug/{slug}/listapi', [HeroInvitationApiController::class, 'listapi']);

Route::post('/guest-messages/{slug}', [GuestMessageController::class, 'store']);
Route::get('/guest-messages/{slug}', [GuestMessageController::class, 'index']);



// Static Files Route dengan CORS support
Route::get('/storage/{path}', function ($path) {
    $storagePath = storage_path('app/public/' . $path);
    
    if (!file_exists($storagePath)) {
        return response()->json([
            'success' => false,
            'message' => 'File not found: ' . $path
        ], 404);
    }

    $file = file_get_contents($storagePath);
    $type = mime_content_type($storagePath);

    return response($file, 200)
        ->header('Content-Type', $type)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept')
        ->header('Access-Control-Allow-Credentials', 'true');
})->where('path', '.*');

// Public Files Route (untuk files di folder public)
Route::get('/public/{path}', function ($path) {
    $publicPath = public_path($path);
    
    if (!file_exists($publicPath)) {
        return response()->json([
            'success' => false,
            'message' => 'File not found: ' . $path
        ], 404);
    }

    $file = file_get_contents($publicPath);
    $type = mime_content_type($publicPath);

    return response($file, 200)
        ->header('Content-Type', $type)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept')
        ->header('Access-Control-Allow-Credentials', 'true');
})->where('path', '.*');

// Preflight handler untuk semua OPTIONS request (API routes)
Route::options('/{any}', function () {
    return response()->json([], 200)
        ->header('Access-Control-Allow-Origin', 'http://localhost:5173')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept, Origin')
        ->header('Access-Control-Allow-Credentials', 'true')
        ->header('Access-Control-Max-Age', '86400');
})->where('any', '.*');

// Preflight handler khusus untuk storage routes
Route::options('/storage/{any}', function () {
    return response()->json([], 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept')
        ->header('Access-Control-Allow-Credentials', 'true')
        ->header('Access-Control-Max-Age', '86400');
})->where('any', '.*');

// Preflight handler khusus untuk public routes
Route::options('/public/{any}', function () {
    return response()->json([], 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept')
        ->header('Access-Control-Allow-Credentials', 'true')
        ->header('Access-Control-Max-Age', '86400');
})->where('any', '.*');

// Fallback route untuk gambar yang tidak ditemukan
Route::get('/images/fallback/{type}', function ($type) {
    $fallbackImages = [
        'galeri' => 'path/to/fallback-image.jpg',
    ];
    
    $imagePath = public_path($fallbackImages[$type] ?? 'images/fallback.jpg');
    
    if (!file_exists($imagePath)) {
        abort(404);
    }

    $file = file_get_contents($imagePath);
    $type = mime_content_type($imagePath);

    return response($file, 200)
        ->header('Content-Type', $type)
        ->header('Access-Control-Allow-Origin', '*');
});