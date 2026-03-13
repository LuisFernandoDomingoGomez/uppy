<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('card_designs', function (Blueprint $table) {
            $table->string('logo_horizontal_path')->nullable()->after('main_image_path');
            $table->string('logo_square_path')->nullable()->after('logo_horizontal_path');
        });
    }

    public function down(): void
    {
        Schema::table('card_designs', function (Blueprint $table) {
            $table->dropColumn([
                'logo_horizontal_path',
                'logo_square_path',
            ]);
        });
    }
};
