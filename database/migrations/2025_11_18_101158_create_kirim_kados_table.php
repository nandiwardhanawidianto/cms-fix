<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('kirim_kados', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('slug_list_id');
            $table->string('nama_penerima')->nullable();
            $table->string('no_hp_penerima')->nullable();
            $table->text('alamat_penerima')->nullable();

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
        Schema::dropIfExists('kirim_kados');
    }
};
