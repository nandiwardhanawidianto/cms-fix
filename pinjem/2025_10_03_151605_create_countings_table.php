<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('countings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slug_list_id')->constrained('slug_lists')->onDelete('cascade');
            $table->string('nama_surat');
            $table->text('surat_arab')->nullable(); // opsional
            $table->text('deskripsi_surat'); // bahasa Indonesia
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countings');
    }
};
