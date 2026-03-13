<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('card_designs', function (Blueprint $table) {
            $table->string('preview_platform')->default('ios')->after('logo_square_path');
        });
    }

    public function down(): void
    {
        Schema::table('card_designs', function (Blueprint $table) {
            $table->dropColumn('preview_platform');
        });
    }
};
