<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Language;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create Persian language in database
        $persianLanguage = [
            'name' => 'Persian',
            'code' => 'fa',
            'direction' => 1, // 1 for RTL, 0 for LTR
            'is_default' => 0
        ];

        Language::create($persianLanguage);

        // Create basic menu for the Persian language
        $data = [];
        $data[] = [
            'text' => 'خانه', "href" => "", "icon" => "empty", "target" => "_self", "title" => "", "type" => "home"
        ];
        $data[] = [
            'text' => 'لیست ها', "href" => "", "icon" => "empty", "target" => "_self", "title" => "", "type" => "listings"
        ];
        $data[] = [
            'text' => 'قیمت ها', "href" => "", "icon" => "empty", "target" => "_self", "title" => "", "type" => "pricing"
        ];
        $data[] = [
            'text' => 'فروشندگان', "href" => "", "icon" => "empty", "target" => "_self", "title" => "", "type" => "vendors"
        ];
        $data[] = [
            'text' => 'فروشگاه', "href" => "", "icon" => "empty", "target" => "_self", "title" => "", "type" => "shop"
        ];
        $data[] = [
            'text' => 'وبلاگ', "href" => "", "icon" => "empty", "target" => "_self", "title" => "", "type" => "blog"
        ];
        $data[] = [
            'text' => 'سوالات متداول', "href" => "", "icon" => "empty", "target" => "_self", "title" => "", "type" => "faq"
        ];
        $data[] = [
            'text' => 'درباره ما', "href" => "", "icon" => "empty", "target" => "_self", "title" => "", "type" => "about-us"
        ];
        $data[] = [
            'text' => 'تماس', "href" => "", "icon" => "empty", "target" => "_self", "title" => "", "type" => "contact"
        ];

        // Get the newly created language
        $language = Language::where('code', 'fa')->first();
        
        // Create menu for Persian language
        \App\Models\MenuBuilder::create([
            'language_id' => $language->id,
            'menus' => json_encode($data, true)
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Get the Persian language
        $language = Language::where('code', 'fa')->first();
        
        // Delete the menu for Persian language
        if ($language) {
            \App\Models\MenuBuilder::where('language_id', $language->id)->delete();
            
            // Delete the language
            $language->delete();
        }
    }
};
