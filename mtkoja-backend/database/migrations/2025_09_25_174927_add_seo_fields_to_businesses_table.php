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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('businesses', 'meta_title')) {
                $columnsToDrop[] = 'meta_title';
            }
            if (Schema::hasColumn('businesses', 'meta_description')) {
                $columnsToDrop[] = 'meta_description';
            }
            if (Schema::hasColumn('businesses', 'keywords')) {
                $columnsToDrop[] = 'keywords';
            }
            if (Schema::hasColumn('businesses', 'full_description')) {
                $columnsToDrop[] = 'full_description';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
