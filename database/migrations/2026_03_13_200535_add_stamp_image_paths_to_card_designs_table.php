<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('card_designs', function (Blueprint $table) {
            $table->string('stamp_active_image_path')->nullable()->after('main_image_path');
            $table->string('stamp_inactive_image_path')->nullable()->after('stamp_active_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('card_designs', function (Blueprint $table) {
            $table->dropColumn([
                'stamp_active_image_path',
                'stamp_inactive_image_path',
            ]);
        });
    }
};
