<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slug_lists', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                 // nama/label
            $table->string('slug')->unique();       // slug unik
            $table->text('keterangan')->nullable(); // keterangan (opsional)
            $table->timestamps();
            $table->timestamp('hosting_at')->nullable(); // kapan dihosting

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slug_lists');
    }
};
