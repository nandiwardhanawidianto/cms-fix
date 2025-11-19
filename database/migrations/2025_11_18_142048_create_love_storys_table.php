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
        Schema::create('love_storys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('slug_list_id');

            $table->text('awal_pertemuan')->nullable();
            $table->text('menjalin_hubungan')->nullable();
            $table->text('Lamaran')->nullable();

            $table->string('gambar_awal')->nullable();
            $table->string('gambar_hubungan')->nullable();
            $table->string('gambar_lamaran')->nullable();
            
            $table->timestamps();

            $table->foreign('slug_list_id')
                ->references('id')
                ->on('slug_lists')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('love_storys');
    }
};
