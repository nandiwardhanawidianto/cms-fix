<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('galeris', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('slug_list_id');

        // Bisa simpan array path gambar, contoh: ["img1.jpg", "img2.jpg"]
        $table->json('carousel_atas')->nullable();
        $table->json('carousel_bawah')->nullable();

        $table->timestamps();

        $table->foreign('slug_list_id')
              ->references('id')->on('slug_lists')
              ->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galeri');
    }
};
