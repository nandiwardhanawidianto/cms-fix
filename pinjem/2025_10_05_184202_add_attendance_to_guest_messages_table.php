<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_messages', function (Blueprint $table) {
            $table->enum('attendance', ['Hadir', 'Tidak Hadir'])
                  ->default('Hadir')
                  ->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('guest_messages', function (Blueprint $table) {
            $table->dropColumn('attendance');
        });
    }
};
