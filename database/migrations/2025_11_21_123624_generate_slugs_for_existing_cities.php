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
        // Generate slugs for existing cities that don't have slug
        $cities = DB::table('cities')
            ->where(function($query) {
                $query->whereNull('slug')
                      ->orWhere('slug', '');
            })
            ->get();
            
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
        // No need to reverse - we can't know which slugs were auto-generated
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
