<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_notifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('card_id')
                ->constrained('cards')
                ->cascadeOnDelete();

            $table->boolean('birthday_enabled')->default(false);
            $table->unsignedInteger('birthday_days_before')->nullable();
            $table->text('birthday_message')->nullable();

            $table->boolean('last_visit_enabled')->default(false);
            $table->unsignedInteger('last_visit_days')->nullable();
            $table->text('last_visit_message')->nullable();

            $table->boolean('expiration_enabled')->default(false);
            $table->text('expiration_message')->nullable();

            $table->boolean('purchase_enabled')->default(false);
            $table->text('purchase_message')->nullable();

            $table->boolean('reward_enabled')->default(false);
            $table->text('reward_message')->nullable();

            $table->boolean('geo_enabled')->default(false);
            $table->unsignedInteger('geo_radius_meters')->nullable();

            $table->json('settings_json')->nullable();

            $table->timestamps();

            $table->unique('card_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_notifications');
    }
};
