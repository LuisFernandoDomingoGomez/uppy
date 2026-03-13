<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_designs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('card_id')
                ->constrained('cards')
                ->cascadeOnDelete();

            $table->string('main_image_path')->nullable();

            $table->string('stamp_active_icon_type')->nullable();   // preset, upload, svg, emoji
            $table->string('stamp_active_icon_value')->nullable();

            $table->string('stamp_inactive_icon_type')->nullable();
            $table->string('stamp_inactive_icon_value')->nullable();

            $table->string('background_color')->nullable();
            $table->string('active_color')->nullable();
            $table->string('inactive_color')->nullable();
            $table->string('text_color')->nullable();

            $table->string('logo_path')->nullable();

            $table->json('preview_json')->nullable();

            $table->timestamps();

            $table->unique('card_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_designs');
    }
};
