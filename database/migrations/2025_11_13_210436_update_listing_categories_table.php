<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listing_categories', function (Blueprint $table) {
            // Add new fields if they don't exist
            if (!Schema::hasColumn('listing_categories', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('listing_categories', 'description')) {
                $table->text('description')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('listing_categories', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('description');
            }
            if (!Schema::hasColumn('listing_categories', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('listing_categories', 'meta_keywords')) {
                $table->string('meta_keywords')->nullable()->after('meta_description');
            }
        });
        
        // Add foreign key separately if parent_id exists
        if (Schema::hasColumn('listing_categories', 'parent_id')) {
            Schema::table('listing_categories', function (Blueprint $table) {
                // Check if foreign key doesn't exist
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'listing_categories' 
                    AND COLUMN_NAME = 'parent_id' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                
                if (empty($foreignKeys)) {
                    $table->foreign('parent_id')->references('id')->on('listing_categories')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listing_categories', function (Blueprint $table) {
            // Drop foreign key if exists
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'listing_categories' 
                AND COLUMN_NAME = 'parent_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (!empty($foreignKeys)) {
                $table->dropForeign([$foreignKeys[0]->CONSTRAINT_NAME]);
            }
            
            // Remove new fields if they exist
            if (Schema::hasColumn('listing_categories', 'parent_id')) {
                $table->dropColumn('parent_id');
            }
            if (Schema::hasColumn('listing_categories', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('listing_categories', 'meta_title')) {
                $table->dropColumn('meta_title');
            }
            if (Schema::hasColumn('listing_categories', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
            if (Schema::hasColumn('listing_categories', 'meta_keywords')) {
                $table->dropColumn('meta_keywords');
            }
        });
    }
};
