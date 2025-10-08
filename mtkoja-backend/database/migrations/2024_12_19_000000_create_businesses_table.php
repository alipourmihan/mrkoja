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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('slug_or_link');
            $table->text('address')->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('category', 100)->nullable();
            $table->float('rating')->nullable();
            $table->integer('reviews_count')->nullable();
            $table->datetime('scraped_at');
            $table->timestamps();
            
            // Indexes for better performance
            $table->unique('slug_or_link');
            $table->index('category');
            $table->index('scraped_at');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
