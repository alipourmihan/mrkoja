<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Listing\ListingContent;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // پیدا کردن همه listing_contents که slug خالی دارند یا null هستند
        $listingContents = ListingContent::where(function($query) {
            $query->whereNull('slug')
                  ->orWhere('slug', '')
                  ->orWhere('slug', '=', '');
        })->get();

        foreach ($listingContents as $content) {
            if (!empty($content->title)) {
                // ساخت slug از title
                $slug = $this->createSlug($content->title);
                
                // اگر slug هنوز خالی است، از listing_id استفاده می‌کنیم
                if (empty($slug) || trim($slug) == '') {
                    $slug = 'listing-' . $content->listing_id;
                }
                
                // بررسی اینکه slug تکراری نباشد
                $existingSlug = ListingContent::where('slug', $slug)
                    ->where('id', '!=', $content->id)
                    ->first();
                
                if ($existingSlug) {
                    $slug = $slug . '-' . $content->listing_id;
                }
                
                $content->slug = $slug;
                $content->save();
            } else {
                // اگر title هم خالی است، از listing_id استفاده می‌کنیم
                $content->slug = 'listing-' . $content->listing_id;
                $content->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // در صورت rollback، نمی‌خواهیم slug ها را حذف کنیم
        // چون ممکن است در آینده نیاز داشته باشیم
    }

    /**
     * ساخت slug از string
     */
    private function createSlug($string)
    {
        $slug = preg_replace('/\s+/u', '-', trim($string));
        $slug = str_replace('/', '', $slug);
        $slug = str_replace('?', '', $slug);
        $slug = str_replace(',', '', $slug);
        $slug = str_replace('،', '', $slug); // کامای فارسی
        $slug = str_replace('؟', '', $slug); // علامت سوال فارسی
        
        return mb_strtolower($slug);
    }
};
