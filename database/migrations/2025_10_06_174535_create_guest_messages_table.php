<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::create('guest_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('slug_list_id'); // relasi ke slug_lists
            $table->string('name', 100);
            $table->string('attendance', 50);
            $table->text('message');
            $table->timestamps();

            // Foreign key ke tabel slug_lists
            $table->foreign('slug_list_id')
                ->references('id')
                ->on('slug_lists')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Rollback migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_messages');
    }
};
