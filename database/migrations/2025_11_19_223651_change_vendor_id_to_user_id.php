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
        // Change vendor_id to user_id in listings table
        if (Schema::hasTable('listings') && Schema::hasColumn('listings', 'vendor_id')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('vendor_id');
            });
            DB::statement('UPDATE listings SET user_id = vendor_id WHERE vendor_id IS NOT NULL');
            Schema::table('listings', function (Blueprint $table) {
                $table->dropColumn('vendor_id');
            });
        }

        // Change vendor_id to user_id in memberships table
        if (Schema::hasTable('memberships') && Schema::hasColumn('memberships', 'vendor_id')) {
            Schema::table('memberships', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('vendor_id');
            });
            DB::statement('UPDATE memberships SET user_id = vendor_id WHERE vendor_id IS NOT NULL');
            Schema::table('memberships', function (Blueprint $table) {
                $table->dropColumn('vendor_id');
            });
        }

        // Change vendor_id to user_id in vendor_infos table
        if (Schema::hasTable('vendor_infos') && Schema::hasColumn('vendor_infos', 'vendor_id')) {
            Schema::table('vendor_infos', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('vendor_id');
            });
            DB::statement('UPDATE vendor_infos SET user_id = vendor_id WHERE vendor_id IS NOT NULL');
            Schema::table('vendor_infos', function (Blueprint $table) {
                $table->dropColumn('vendor_id');
            });
        }

        // Change vendor_id to user_id in feature_orders table
        if (Schema::hasTable('feature_orders') && Schema::hasColumn('feature_orders', 'vendor_id')) {
            Schema::table('feature_orders', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('vendor_id');
            });
            DB::statement('UPDATE feature_orders SET user_id = vendor_id WHERE vendor_id IS NOT NULL');
            Schema::table('feature_orders', function (Blueprint $table) {
                $table->dropColumn('vendor_id');
            });
        }

        // Change vendor_id to user_id in listing_messages table
        if (Schema::hasTable('listing_messages') && Schema::hasColumn('listing_messages', 'vendor_id')) {
            Schema::table('listing_messages', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('vendor_id');
            });
            DB::statement('UPDATE listing_messages SET user_id = vendor_id WHERE vendor_id IS NOT NULL');
            Schema::table('listing_messages', function (Blueprint $table) {
                $table->dropColumn('vendor_id');
            });
        }

        // Change vendor_id to user_id in product_messages table
        if (Schema::hasTable('product_messages') && Schema::hasColumn('product_messages', 'vendor_id')) {
            Schema::table('product_messages', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('vendor_id');
            });
            DB::statement('UPDATE product_messages SET user_id = vendor_id WHERE vendor_id IS NOT NULL');
            Schema::table('product_messages', function (Blueprint $table) {
                $table->dropColumn('vendor_id');
            });
        }

        // Change vendor_id to user_id in visitors table
        if (Schema::hasTable('visitors') && Schema::hasColumn('visitors', 'vendor_id')) {
            Schema::table('visitors', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('vendor_id');
            });
            DB::statement('UPDATE visitors SET user_id = vendor_id WHERE vendor_id IS NOT NULL');
            Schema::table('visitors', function (Blueprint $table) {
                $table->dropColumn('vendor_id');
            });
        }

        // Change vendor_id to user_id in transactions table
        if (Schema::hasTable('transcations') && Schema::hasColumn('transcations', 'vendor_id')) {
            Schema::table('transcations', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('vendor_id');
            });
            DB::statement('UPDATE transcations SET user_id = vendor_id WHERE vendor_id IS NOT NULL');
            Schema::table('transcations', function (Blueprint $table) {
                $table->dropColumn('vendor_id');
            });
        }

        // Change vendor_id to user_id in listing_products table
        if (Schema::hasTable('listing_products') && Schema::hasColumn('listing_products', 'vendor_id')) {
            Schema::table('listing_products', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('vendor_id');
            });
            DB::statement('UPDATE listing_products SET user_id = vendor_id WHERE vendor_id IS NOT NULL');
            Schema::table('listing_products', function (Blueprint $table) {
                $table->dropColumn('vendor_id');
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
        // Reverse: change user_id back to vendor_id
        $tables = [
            'listings' => 'user_id',
            'memberships' => 'user_id',
            'vendor_infos' => 'user_id',
            'feature_orders' => 'user_id',
            'listing_messages' => 'user_id',
            'product_messages' => 'user_id',
            'visitors' => 'user_id',
            'transcations' => 'user_id',
            'listing_products' => 'user_id'
        ];

        foreach ($tables as $table => $column) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                Schema::table($table, function (Blueprint $table) use ($column) {
                    $table->unsignedBigInteger('vendor_id')->nullable()->after($column);
                });
                DB::statement("UPDATE {$table} SET vendor_id = {$column} WHERE {$column} IS NOT NULL");
                Schema::table($table, function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
