<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            if (!Schema::hasColumn('cities', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }
        });

        // Generate slugs for existing cities
        $cities = DB::table('cities')->whereNull('slug')->orWhere('slug', '')->get();
        foreach ($cities as $city) {
            if ($city->name) {
                $slug = $this->createSlug($city->name);
                // Make sure slug is unique
                $uniqueSlug = $slug;
                $counter = 1;
                while (DB::table('cities')->where('slug', $uniqueSlug)->where('id', '!=', $city->id)->exists()) {
                    $uniqueSlug = $slug . '-' . $counter;
                    $counter++;
                }
                DB::table('cities')->where('id', $city->id)->update(['slug' => $uniqueSlug]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            if (Schema::hasColumn('cities', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }

    /**
     * Create slug from string
     */
    private function createSlug($string)
    {
        $slug = preg_replace('/\s+/u', '-', trim($string));
        $slug = str_replace('/', '', $slug);
        $slug = str_replace('?', '', $slug);
        $slug = str_replace(',', '', $slug);
        return mb_strtolower($slug);
    }
};
