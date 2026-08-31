<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_invitations', function (Blueprint $table) {
            $table->string('nama_panggilan_pria')->nullable()->change();
            $table->string('nama_lengkap_pria')->nullable()->change();
            $table->string('orangtua_pria')->nullable()->change();
            $table->string('foto_pria')->nullable()->change();

            $table->string('nama_panggilan_wanita')->nullable()->change();
            $table->string('nama_lengkap_wanita')->nullable()->change();
            $table->string('orangtua_wanita')->nullable()->change();
            $table->string('foto_wanita')->nullable()->change();
        });
    }

    public function down(): void
    {
        //
    }
};