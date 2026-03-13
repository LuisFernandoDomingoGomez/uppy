<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->boolean('supports_rewards')->default(false);
            $table->boolean('supports_balance')->default(false);
            $table->boolean('supports_stamps')->default(false);
            $table->boolean('supports_notifications')->default(true);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_types');
    }
};
