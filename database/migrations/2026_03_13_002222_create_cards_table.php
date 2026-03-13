<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();

            $table->foreignId('card_type_id')
                ->constrained('card_types')
                ->cascadeOnDelete();

            $table->string('name')->nullable();
            $table->string('slug')->nullable()->unique();

            $table->string('status')->default('draft'); // draft, active, inactive, archived
            $table->string('code_type')->default('qr'); // qr, barcode

            $table->string('main_image_path')->nullable();
            $table->text('terms')->nullable();

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('published_at')->nullable();

            $table->boolean('is_unlimited')->default(true);
            $table->boolean('is_active')->default(false);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->json('settings_json')->nullable();
            $table->json('meta_json')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
