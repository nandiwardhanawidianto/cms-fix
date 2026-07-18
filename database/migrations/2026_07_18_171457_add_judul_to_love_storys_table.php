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
        Schema::table('love_storys', function (Blueprint $table) {
        $table->string('judul_awal_pertemuan')
              ->nullable()
              ->after('slug_list_id');

        $table->string('judul_menjalin_hubungan')
              ->nullable()
              ->after('menjalin_hubungan');

        $table->string('judul_lamaran')
              ->nullable()
              ->after('lamaran');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('love_storys', function (Blueprint $table) {
             $table->dropColumn([
            'judul_awal_pertemuan',
            'judul_menjalin_hubungan',
            'judul_lamaran'
        ]);
        });
    }
};
