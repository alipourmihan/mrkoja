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
        Schema::table('businesses', function (Blueprint $table) {
            // SEO Fields
            $table->string('meta_title')->nullable()->after('description');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('keywords')->nullable()->after('meta_description');
            $table->longText('full_description')->nullable()->after('keywords');
            
            // Social Fields
            $table->string('instagram')->nullable()->after('website');
            $table->string('whatsapp')->nullable()->after('instagram');
            
            // Admin-only location fields
            $table->unsignedBigInteger('province_id')->nullable()->after('longitude');
            $table->unsignedBigInteger('city_id')->nullable()->after('province_id');
            $table->unsignedBigInteger('neighborhood_id')->nullable()->after('city_id');
            
            // Foreign key constraints
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('set null');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            $table->foreign('neighborhood_id')->references('id')->on('neighborhoods')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['neighborhood_id']);
            
            $table->dropColumn([
                'meta_title', 'meta_description', 'keywords', 'full_description',
                'instagram', 'whatsapp', 'province_id', 'city_id', 'neighborhood_id'
            ]);
        });
    }
};
