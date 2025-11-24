<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (Schema::hasColumn('listings', 'min_price')) {
                $table->dropColumn('min_price');
            }

            if (Schema::hasColumn('listings', 'max_price')) {
                $table->dropColumn('max_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (!Schema::hasColumn('listings', 'min_price')) {
                $table->double('min_price')->nullable()->after('status');
            }

            if (!Schema::hasColumn('listings', 'max_price')) {
                $table->double('max_price')->nullable()->after('min_price');
            }
        });
    }
};

