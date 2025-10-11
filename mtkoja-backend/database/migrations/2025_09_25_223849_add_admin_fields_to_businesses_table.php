<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Check if a foreign key constraint exists
     */
    private function foreignKeyExists($table, $constraintName): bool
    {
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND CONSTRAINT_NAME = ?
        ", [$table, $constraintName]);
        
        return count($constraints) > 0;
    }
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            // SEO Fields (only add if they don't exist)
            if (!Schema::hasColumn('businesses', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('description');
            }
            if (!Schema::hasColumn('businesses', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('businesses', 'keywords')) {
                $table->text('keywords')->nullable()->after('meta_description');
            }
            if (!Schema::hasColumn('businesses', 'full_description')) {
                $table->longText('full_description')->nullable()->after('keywords');
            }
            
            // Social Fields (only add if they don't exist)
            if (!Schema::hasColumn('businesses', 'instagram')) {
                $table->string('instagram')->nullable()->after('website');
            }
            if (!Schema::hasColumn('businesses', 'whatsapp')) {
                $table->string('whatsapp')->nullable()->after('instagram');
            }
            
            // Admin-only location fields (only add if they don't exist)
            if (!Schema::hasColumn('businesses', 'province_id')) {
                $table->unsignedBigInteger('province_id')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('businesses', 'city_id')) {
                $table->unsignedBigInteger('city_id')->nullable()->after('province_id');
            }
            if (!Schema::hasColumn('businesses', 'neighborhood_id')) {
                $table->unsignedBigInteger('neighborhood_id')->nullable()->after('city_id');
            }
            
            // Foreign key constraints (only add if they don't exist)
            if (!Schema::hasColumn('businesses', 'province_id') || !$this->foreignKeyExists('businesses', 'businesses_province_id_foreign')) {
                $table->foreign('province_id')->references('id')->on('provinces')->onDelete('set null');
            }
            if (!Schema::hasColumn('businesses', 'city_id') || !$this->foreignKeyExists('businesses', 'businesses_city_id_foreign')) {
                $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            }
            if (!Schema::hasColumn('businesses', 'neighborhood_id') || !$this->foreignKeyExists('businesses', 'businesses_neighborhood_id_foreign')) {
                $table->foreign('neighborhood_id')->references('id')->on('neighborhoods')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            // Drop foreign keys if they exist
            if ($this->foreignKeyExists('businesses', 'businesses_province_id_foreign')) {
                $table->dropForeign(['province_id']);
            }
            if ($this->foreignKeyExists('businesses', 'businesses_city_id_foreign')) {
                $table->dropForeign(['city_id']);
            }
            if ($this->foreignKeyExists('businesses', 'businesses_neighborhood_id_foreign')) {
                $table->dropForeign(['neighborhood_id']);
            }
            
            // Drop columns if they exist
            $columnsToDrop = [];
            $columns = ['meta_title', 'meta_description', 'keywords', 'full_description', 'instagram', 'whatsapp', 'province_id', 'city_id', 'neighborhood_id'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('businesses', $column)) {
                    $columnsToDrop[] = $column;
                }
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
