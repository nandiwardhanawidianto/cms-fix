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
        Schema::table('slug_lists', function (Blueprint $table) {
            $table->string('theme')->default('violet')->after('keterangan');
        });
    }

    public function down()
    {
        Schema::table('slug_lists', function (Blueprint $table) {
            $table->dropColumn('theme');
        });
    }
};
