<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_registration_fields', function (Blueprint $table) {
            $table->id();

            $table->foreignId('card_id')
                ->constrained('cards')
                ->cascadeOnDelete();

            $table->string('field_key');
            $table->string('label');
            $table->string('type')->default('text'); // text, email, phone, select, date, etc.

            $table->boolean('is_required')->default(false);
            $table->json('options_json')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_registration_fields');
    }
};
