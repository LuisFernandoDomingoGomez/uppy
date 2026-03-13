<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_configs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('card_id')
                ->constrained('cards')
                ->cascadeOnDelete();

            $table->string('key');
            $table->json('value_json')->nullable();

            $table->timestamps();

            $table->unique(['card_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_configs');
    }
};
