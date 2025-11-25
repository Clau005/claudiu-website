<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Themes table
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('version')->default('1.0.0');
            $table->string('author')->nullable();
            $table->boolean('is_active')->default(false);
            $table->json('settings')->nullable(); // Theme-level settings (colors, fonts, etc.)
            $table->timestamps();
            $table->softDeletes();
        });

        // Pages table (belongs to theme)
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Home", "Product", "Collection"
            $table->string('slug'); // e.g., "home", "product", "collection"
            $table->string('type'); // e.g., "static", "dynamic", "template"
            $table->string('context_key')->nullable(); // e.g., "product", "collection"
            $table->string('route_pattern')->nullable(); // e.g., "/products/{slug}"
            $table->boolean('is_published')->default(false);
            $table->json('draft_config')->nullable(); // Draft sections configuration
            $table->json('published_config')->nullable(); // Published sections configuration
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['theme_id', 'slug']);
        });

        // Theme sections (available sections for a theme)
        Schema::create('theme_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_id')->constrained()->onDelete('cascade');
            $table->string('section_key'); // References section registry
            $table->integer('order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->unique(['theme_id', 'section_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_sections');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('themes');
    }
};
