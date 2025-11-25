<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->json('header_config_draft')->nullable()->after('header_config');
            $table->json('footer_config_draft')->nullable()->after('footer_config');
        });
        
        // Copy existing header/footer config to draft for existing themes
        \DB::statement('UPDATE themes SET header_config_draft = header_config WHERE header_config IS NOT NULL');
        \DB::statement('UPDATE themes SET footer_config_draft = footer_config WHERE footer_config IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn(['header_config_draft', 'footer_config_draft']);
        });
    }
};
