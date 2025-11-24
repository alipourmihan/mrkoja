<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_listing_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id');
            $table->unsignedBigInteger('listing_category_id');
            $table->timestamps();

            $table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');
            $table->foreign('listing_category_id')->references('id')->on('listing_categories')->onDelete('cascade');
            
            // جلوگیری از تکراری بودن
            $table->unique(['listing_id', 'listing_category_id']);
            
            // اضافه کردن index برای بهبود performance
            $table->index('listing_id');
            $table->index('listing_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listing_listing_category');
    }
};
