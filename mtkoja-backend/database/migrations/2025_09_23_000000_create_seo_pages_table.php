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
        Schema::create('seo_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('neighborhood_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('meta_description', 500);
            $table->longText('content')->nullable();
            $table->string('h1')->nullable();
            $table->string('og_image')->nullable();
            $table->text('custom_text')->nullable();
            $table->timestamps();

            // Unique combination: category + city + neighborhood (province optional)
            $table->unique(['category_id', 'city_id', 'neighborhood_id'], 'seo_pages_unique_combo');

            // Helpful indexes
            $table->index(['category_id']);
            $table->index(['province_id']);
            $table->index(['city_id']);
            $table->index(['neighborhood_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_pages');
    }
};


