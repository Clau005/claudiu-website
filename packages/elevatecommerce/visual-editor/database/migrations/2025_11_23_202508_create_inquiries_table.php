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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            
            // Contact Information
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            
            // Inquiry Details
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            
            // Additional Fields (flexible JSON for custom fields)
            $table->json('custom_fields')->nullable();
            
            // Metadata
            $table->string('type')->default('general'); // general, support, sales, partnership, etc.
            $table->string('status')->default('new'); // new, read, replied, closed
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->string('source')->nullable(); // contact-form, footer-form, etc.
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            
            // Admin Notes
            $table->text('admin_notes')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('replied_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('type');
            $table->index('created_at');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
