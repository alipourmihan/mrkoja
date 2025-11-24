<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('listing_contents', function (Blueprint $table) {
            if (!Schema::hasColumn('listing_contents', 'neighborhood_id')) {
                $table->unsignedBigInteger('neighborhood_id')->nullable()->after('city_id');
                $table->foreign('neighborhood_id')
                    ->references('id')
                    ->on('neighborhoods')
                    ->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('listing_contents', function (Blueprint $table) {
            if (Schema::hasColumn('listing_contents', 'neighborhood_id')) {
                $table->dropForeign(['neighborhood_id']);
                $table->dropColumn('neighborhood_id');
            }
        });
    }
};
