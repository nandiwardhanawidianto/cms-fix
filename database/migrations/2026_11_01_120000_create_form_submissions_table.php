<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 80)->unique();
            $table->string('customer_whatsapp', 30);
            $table->string('shopee_username', 120)->nullable();
            $table->string('display_order', 20)->default('female_first');
            $table->string('photo_package', 20)->nullable();
            $table->unsignedTinyInteger('photo_count')->default(0);
            $table->json('payload');
            $table->json('raw_form')->nullable();
            $table->string('status', 30)->default('pending_review');
            $table->unsignedBigInteger('published_slug_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
    }
};
