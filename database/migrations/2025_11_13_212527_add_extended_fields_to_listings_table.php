<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->string('mobile_phone', 20)->nullable()->after('phone');
            $table->string('landline_phone', 20)->nullable()->after('mobile_phone');
            $table->string('website')->nullable()->after('landline_phone');
            $table->string('instagram')->nullable()->after('website');
            $table->string('telegram')->nullable()->after('instagram');
            $table->string('whatsapp', 20)->nullable()->after('telegram');
        });

        Schema::table('listing_contents', function (Blueprint $table) {
            $table->string('short_description', 255)->nullable()->after('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listing_contents', function (Blueprint $table) {
            $table->dropColumn('short_description');
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn([
                'mobile_phone',
                'landline_phone',
                'website',
                'instagram',
                'telegram',
                'whatsapp',
            ]);
        });
    }
};
