<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_tiers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('card_id')
                ->constrained('cards')
                ->cascadeOnDelete();

            $table->string('tier_type'); // cashback_level, discount_level, etc.
            $table->string('name')->nullable();

            $table->decimal('threshold_amount', 12, 2)->nullable();
            $table->decimal('percentage', 8, 2)->nullable();
            $table->decimal('reward_value', 12, 2)->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->json('meta_json')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_tiers');
    }
};
