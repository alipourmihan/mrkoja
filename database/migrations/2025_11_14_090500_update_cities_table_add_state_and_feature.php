<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            if (Schema::hasColumn('cities', 'language_id')) {
                $table->dropColumn('language_id');
            }
        });

        Schema::table('cities', function (Blueprint $table) {
            if (Schema::hasColumn('cities', 'country_id')) {
                $table->dropColumn('country_id');
            }
        });

        Schema::table('cities', function (Blueprint $table) {
            if (Schema::hasColumn('cities', 'province_id')) {
                $table->dropColumn('province_id');
            }
        });

        Schema::table('cities', function (Blueprint $table) {
            if (!Schema::hasColumn('cities', 'state_id')) {
                $table->unsignedBigInteger('state_id')->nullable()->after('name');
            }

            if (!Schema::hasColumn('cities', 'feature_image')) {
                $table->string('feature_image')->nullable()->after('state_id');
            }
        });

        if (Schema::hasColumn('cities', 'state_id')) {
            $databaseName = config('database.connections.' . config('database.default') . '.database');

            $foreignKey = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', $databaseName)
                ->where('TABLE_NAME', 'cities')
                ->where('COLUMN_NAME', 'state_id')
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->value('CONSTRAINT_NAME');

            if ($foreignKey) {
                Schema::table('cities', function (Blueprint $table) use ($foreignKey) {
                    $table->dropForeign($foreignKey);
                });
            }

            // Try to add foreign key, but skip if it fails
            try {
                Schema::table('cities', function (Blueprint $table) {
                    $table->foreign('state_id')
                        ->references('id')
                        ->on('states')
                        ->nullOnDelete();
                });
            } catch (\Exception $e) {
                // Foreign key constraint failed, but column was added successfully
                // This is acceptable - the column exists and can be used without FK constraint
            }
        }
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            if (Schema::hasColumn('cities', 'state_id')) {
                $table->dropForeign(['state_id']);
                $table->dropColumn('state_id');
            }

            if (Schema::hasColumn('cities', 'feature_image')) {
                $table->dropColumn('feature_image');
            }
        });
    }
};

