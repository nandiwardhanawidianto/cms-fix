<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('song_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slug_list_id')->constrained('slug_lists')->onDelete('cascade');
            $table->foreignId('song_id')->constrained('songs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('song_lists');
    }
};
