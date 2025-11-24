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
        // Only merge if vendors table exists and has data
        if (!Schema::hasTable('vendors')) {
            return;
        }

        // Add temporary column to track old vendor_id
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'old_vendor_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('old_vendor_id')->nullable()->after('id');
            });
        }

        // Merge vendors data into users table
        // First, update existing users that have matching email with vendors
        // Only update columns that exist in vendors table
        $updateFields = [
            "u.role = 'business'",
            "u.to_mail = COALESCE(v.to_mail, u.to_mail)",
            "u.amount = COALESCE(v.amount, 0)",
            "u.avg_rating = COALESCE(v.avg_rating, 0)",
            "u.show_email_addresss = COALESCE(v.show_email_addresss, 0)",
            "u.show_phone_number = COALESCE(v.show_phone_number, 0)",
            "u.show_contact_form = COALESCE(v.show_contact_form, 0)",
            "u.photo = COALESCE(v.photo, u.photo)",
            "u.old_vendor_id = v.id"
        ];

        // Add optional fields if they exist in vendors table
        if (Schema::hasColumn('vendors', 'facebook')) {
            $updateFields[] = "u.facebook = COALESCE(v.facebook, u.facebook)";
        }
        if (Schema::hasColumn('vendors', 'twitter')) {
            $updateFields[] = "u.twitter = COALESCE(v.twitter, u.twitter)";
        }
        if (Schema::hasColumn('vendors', 'linkedin')) {
            $updateFields[] = "u.linkedin = COALESCE(v.linkedin, u.linkedin)";
        }

        $updateSql = "UPDATE users u
            INNER JOIN vendors v ON u.email = v.email
            SET " . implode(",\n                ", $updateFields) . "
            WHERE u.role = 'user' OR u.role IS NULL";

        DB::statement($updateSql);

        // Insert vendors that don't have matching email in users
        $insertFields = [
            'old_vendor_id',
            'role',
            'username',
            'email',
            'password',
            'phone',
            'photo',
            'status',
            'email_verified_at',
            'to_mail',
            'amount',
            'avg_rating',
            'show_email_addresss',
            'show_phone_number',
            'show_contact_form',
            'created_at',
            'updated_at'
        ];

        $selectFields = [
            "v.id as old_vendor_id",
            "'business' as role",
            "v.username",
            "v.email",
            "v.password",
            "v.phone",
            "v.photo",
            "COALESCE(v.status, 1) as status",
            "v.email_verified_at",
            "v.to_mail",
            "COALESCE(v.amount, 0) as amount",
            "COALESCE(v.avg_rating, 0) as avg_rating",
            "COALESCE(v.show_email_addresss, 0) as show_email_addresss",
            "COALESCE(v.show_phone_number, 0) as show_phone_number",
            "COALESCE(v.show_contact_form, 0) as show_contact_form",
            "v.created_at",
            "v.updated_at"
        ];

        // Add optional fields if they exist
        if (Schema::hasColumn('vendors', 'facebook')) {
            $insertFields[] = 'facebook';
            $selectFields[] = 'v.facebook';
        }
        if (Schema::hasColumn('vendors', 'twitter')) {
            $insertFields[] = 'twitter';
            $selectFields[] = 'v.twitter';
        }
        if (Schema::hasColumn('vendors', 'linkedin')) {
            $insertFields[] = 'linkedin';
            $selectFields[] = 'v.linkedin';
        }

        $insertSql = "INSERT INTO users (" . implode(', ', $insertFields) . ")
            SELECT " . implode(",\n                ", $selectFields) . "
            FROM vendors v
            WHERE NOT EXISTS (
                SELECT 1 FROM users u WHERE u.email = v.email
            )";

        DB::statement($insertSql);

        // Update foreign keys in related tables
        // Update listings table
        if (Schema::hasTable('listings') && Schema::hasColumn('listings', 'user_id')) {
            DB::statement("
                UPDATE listings l
                INNER JOIN users u ON l.user_id = u.old_vendor_id
                SET l.user_id = u.id
                WHERE u.old_vendor_id IS NOT NULL
            ");
        }

        // Update memberships table
        if (Schema::hasTable('memberships') && Schema::hasColumn('memberships', 'user_id')) {
            DB::statement("
                UPDATE memberships m
                INNER JOIN users u ON m.user_id = u.old_vendor_id
                SET m.user_id = u.id
                WHERE u.old_vendor_id IS NOT NULL
            ");
        }

        // Update vendor_infos table
        if (Schema::hasTable('vendor_infos') && Schema::hasColumn('vendor_infos', 'user_id')) {
            DB::statement("
                UPDATE vendor_infos vi
                INNER JOIN users u ON vi.user_id = u.old_vendor_id
                SET vi.user_id = u.id
                WHERE u.old_vendor_id IS NOT NULL
            ");
        }

        // Update support_tickets table (for vendor_type)
        if (Schema::hasTable('support_tickets') && Schema::hasColumn('support_tickets', 'user_id')) {
            DB::statement("
                UPDATE support_tickets st
                INNER JOIN users u ON st.user_id = u.old_vendor_id
                SET st.user_id = u.id
                WHERE st.user_type = 'vendor' AND u.old_vendor_id IS NOT NULL
            ");
        }

        // Update other tables with vendor_id
        $tables = ['feature_orders', 'listing_messages', 'product_messages', 'visitors', 'transcations', 'listing_products'];
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'user_id')) {
                DB::statement("
                    UPDATE {$table} t
                    INNER JOIN users u ON t.user_id = u.old_vendor_id
                    SET t.user_id = u.id
                    WHERE u.old_vendor_id IS NOT NULL
                ");
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
        // Note: This is a destructive operation and should be used with caution
        // In production, you might want to keep the merged data
        
        // Remove old_vendor_id column
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'old_vendor_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('old_vendor_id');
            });
        }

        // Reset role for merged users (optional - comment out if you want to keep the data)
        // DB::statement("UPDATE users SET role = 'user' WHERE old_vendor_id IS NOT NULL");
    }
};
