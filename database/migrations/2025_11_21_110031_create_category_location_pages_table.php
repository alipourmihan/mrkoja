<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('category_location_pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('neighborhood_id')->nullable();
            $table->string('title'); // مثال: "بهترین باشگاه‌های تهران"
            $table->string('slug')->unique();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('description')->nullable(); // محتوای صفحه
            $table->string('featured_image')->nullable();
            $table->integer('status')->default(1); // 1=active, 0=inactive
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Foreign keys
            $table->foreign('category_id')->references('id')->on('listing_categories')->nullOnDelete();
            $table->foreign('state_id')->references('id')->on('states')->nullOnDelete();
            $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
            $table->foreign('neighborhood_id')->references('id')->on('neighborhoods')->nullOnDelete();

            // Indexes
            $table->index('slug');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('category_location_pages');
    }
};
