<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('acaras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('slug_list_id');
            $table->string('nama_acara');
            $table->date('tanggal_acara');
            $table->string('pukul_acara');
            $table->text('alamat_acara');
            $table->string('link_acara');
            $table->timestamps();

            $table->foreign('slug_list_id')
                  ->references('id')
                  ->on('slug_lists')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acaras');
    }
};
