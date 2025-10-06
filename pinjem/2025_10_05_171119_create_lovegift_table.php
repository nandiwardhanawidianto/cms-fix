<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lovegifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slug_list_id')->constrained('slug_lists')->onDelete('cascade');
            $table->foreignId('bank_id')->constrained('banks')->onDelete('cascade');
            $table->string('no_rekening');
            $table->string('pemilik_bank');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lovegifts');
    }
};
