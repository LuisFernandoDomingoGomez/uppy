<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('card_id')
                ->constrained('cards')
                ->cascadeOnDelete();

            $table->string('type')->default('url'); // url, whatsapp, phone, instagram, etc.
            $table->string('value')->nullable();
            $table->string('label')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_links');
    }
};
