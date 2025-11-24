<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (!Schema::hasColumn('listings', 'mobile_phone')) {
                $table->string('mobile_phone', 20)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('listings', 'landline_phone')) {
                $table->string('landline_phone', 20)->nullable()->after('mobile_phone');
            }
            if (!Schema::hasColumn('listings', 'website')) {
                $table->string('website')->nullable()->after('landline_phone');
            }
            if (!Schema::hasColumn('listings', 'instagram')) {
                $table->string('instagram')->nullable()->after('website');
            }
            if (!Schema::hasColumn('listings', 'telegram')) {
                $table->string('telegram')->nullable()->after('instagram');
            }
            if (!Schema::hasColumn('listings', 'whatsapp')) {
                $table->string('whatsapp', 20)->nullable()->after('telegram');
            }
        });

        Schema::table('listing_contents', function (Blueprint $table) {
            if (!Schema::hasColumn('listing_contents', 'short_description')) {
                $table->string('short_description', 255)->nullable()->after('slug');
            }
        });
    }

    public function down(): void
    {
        Schema::table('listing_contents', function (Blueprint $table) {
            if (Schema::hasColumn('listing_contents', 'short_description')) {
                $table->dropColumn('short_description');
            }
        });

        Schema::table('listings', function (Blueprint $table) {
            $columns = [
                'mobile_phone',
                'landline_phone',
                'website',
                'instagram',
                'telegram',
                'whatsapp',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('listings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

