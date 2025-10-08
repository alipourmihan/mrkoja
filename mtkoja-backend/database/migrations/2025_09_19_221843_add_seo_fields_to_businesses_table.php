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
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('businesses', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('businesses', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('businesses', 'meta_keywords')) {
                $table->string('meta_keywords')->nullable()->after('meta_description');
            }
            if (!Schema::hasColumn('businesses', 'province_id')) {
                $table->foreignId('province_id')->nullable()->constrained()->onDelete('set null')->after('meta_keywords');
            }
            if (!Schema::hasColumn('businesses', 'city_id')) {
                $table->foreignId('city_id')->nullable()->constrained()->onDelete('set null')->after('province_id');
            }
            if (!Schema::hasColumn('businesses', 'neighborhood_id')) {
                $table->foreignId('neighborhood_id')->nullable()->constrained()->onDelete('set null')->after('city_id');
            }
            if (!Schema::hasColumn('businesses', 'postal_code')) {
                $table->string('postal_code', 10)->nullable()->after('neighborhood_id');
            }
            if (!Schema::hasColumn('businesses', 'view_count')) {
                $table->integer('view_count')->default(0)->after('postal_code');
            }
            if (!Schema::hasColumn('businesses', 'featured_until')) {
                $table->timestamp('featured_until')->nullable()->after('view_count');
            }
            
            // Add indexes
            $table->index(['slug', 'status']);
            $table->index(['province_id', 'city_id', 'neighborhood_id']);
            $table->index(['is_featured', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex(['slug', 'status']);
            $table->dropIndex(['province_id', 'city_id', 'neighborhood_id']);
            $table->dropIndex(['is_featured', 'status']);
            
            $table->dropForeign(['province_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['neighborhood_id']);
            
            $table->dropColumn([
                'meta_title', 'meta_description', 'meta_keywords',
                'province_id', 'city_id', 'neighborhood_id', 'postal_code',
                'is_featured', 'view_count', 'featured_until'
            ]);
        });
    }
};
