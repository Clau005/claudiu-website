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
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            
            // Collection type: 'manual' or 'smart'
            $table->enum('type', ['manual', 'smart'])->default('manual');
            
            // Smart collection conditions (JSON)
            $table->json('conditions')->nullable();
            
            // Page template assignment
            $table->foreignId('page_id')->nullable()->constrained('pages')->nullOnDelete();
            
            // Metafields for SEO and custom data
            $table->json('metafields')->nullable();
            
            // Publishing
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });

        // Polymorphic pivot table for collection items
        Schema::create('collectables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
            
            // Polymorphic relation - can be ANY model
            $table->morphs('collectable'); // Creates collectable_type and collectable_id
            
            // Position for manual ordering
            $table->integer('position')->default(0);
            
            $table->timestamps();
            
            // Unique constraint - same item can't be in collection twice
            $table->unique(['collection_id', 'collectable_type', 'collectable_id'], 'collectables_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collectables');
        Schema::dropIfExists('collections');
    }
};
