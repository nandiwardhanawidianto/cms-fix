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
        Schema::create('love_gifts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('slug_list_id')->constrained('slug_lists')->onDelete('cascade');
        
        $table->json('banks')->nullable(); // simpan data rekening sebagai JSON
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('love_gift');
    }
};
