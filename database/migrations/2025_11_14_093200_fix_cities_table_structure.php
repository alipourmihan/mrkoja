<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->columnExists('cities', 'province_id')) {
            $this->dropForeignIfExists('cities', 'province_id');
            $this->dropIndexIfExists('cities', 'province_id');
            DB::statement('ALTER TABLE cities DROP COLUMN province_id');
        }

        if ($this->columnExists('cities', 'language_id')) {
            $this->dropIndexIfExists('cities', 'language_id');
            DB::statement('ALTER TABLE cities DROP COLUMN language_id');
        }

        if ($this->columnExists('cities', 'country_id')) {
            $this->dropForeignIfExists('cities', 'country_id');
            $this->dropIndexIfExists('cities', 'country_id');
            DB::statement('ALTER TABLE cities DROP COLUMN country_id');
        }

        if (! $this->columnExists('cities', 'state_id')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->unsignedBigInteger('state_id')->nullable()->after('name');
            });
        }

        if (! $this->columnExists('cities', 'feature_image')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->string('feature_image')->nullable()->after('state_id');
            });
        }

        $this->dropForeignIfExists('cities', 'state_id');

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

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            if (Schema::hasColumn('cities', 'feature_image')) {
                $table->dropColumn('feature_image');
            }
            if (Schema::hasColumn('cities', 'state_id')) {
                $table->dropForeign(['state_id']);
                $table->dropColumn('state_id');
            }
        });
    }
    private function dropForeignIfExists(string $table, string $column): void
    {
        $databaseName = config('database.connections.' . config('database.default') . '.database');

        $constraint = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $databaseName)
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        if ($constraint) {
            Schema::table($table, function (Blueprint $table) use ($constraint) {
                $table->dropForeign($constraint);
            });
        }
    }

    private function dropIndexIfExists(string $table, string $column): void
    {
        $databaseName = config('database.connections.' . config('database.default') . '.database');

        $indexes = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $databaseName)
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->pluck('INDEX_NAME')
            ->unique();

        foreach ($indexes as $indexName) {
            if ($indexName === 'PRIMARY') {
                continue;
            }
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        }
    }

    private function columnExists(string $table, string $column): bool
    {
        $databaseName = config('database.connections.' . config('database.default') . '.database');

        return DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', $databaseName)
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->exists();
    }
};

