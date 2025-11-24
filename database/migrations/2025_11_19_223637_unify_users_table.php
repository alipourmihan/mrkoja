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
        Schema::table('users', function (Blueprint $table) {
            // Add role column
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['user', 'business'])->default('user')->after('id');
            }

            // Add vendor-specific columns if they don't exist
            if (!Schema::hasColumn('users', 'to_mail')) {
                $table->string('to_mail')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'amount')) {
                $table->decimal('amount', 10, 2)->default(0)->after('status');
            }
            if (!Schema::hasColumn('users', 'facebook')) {
                $table->string('facebook')->nullable()->after('amount');
            }
            if (!Schema::hasColumn('users', 'twitter')) {
                $table->string('twitter')->nullable()->after('facebook');
            }
            if (!Schema::hasColumn('users', 'linkedin')) {
                $table->string('linkedin')->nullable()->after('twitter');
            }
            if (!Schema::hasColumn('users', 'avg_rating')) {
                $table->decimal('avg_rating', 3, 2)->default(0)->after('linkedin');
            }
            if (!Schema::hasColumn('users', 'show_email_addresss')) {
                $table->boolean('show_email_addresss')->default(0)->after('avg_rating');
            }
            if (!Schema::hasColumn('users', 'show_phone_number')) {
                $table->boolean('show_phone_number')->default(0)->after('show_email_addresss');
            }
            if (!Schema::hasColumn('users', 'show_contact_form')) {
                $table->boolean('show_contact_form')->default(0)->after('show_phone_number');
            }
            if (!Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('image');
            }
        });

        // Add indexes for better performance
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove indexes
            $table->dropIndex(['role']);
            $table->dropIndex(['status']);

            // Remove columns
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'to_mail')) {
                $table->dropColumn('to_mail');
            }
            if (Schema::hasColumn('users', 'amount')) {
                $table->dropColumn('amount');
            }
            if (Schema::hasColumn('users', 'facebook')) {
                $table->dropColumn('facebook');
            }
            if (Schema::hasColumn('users', 'twitter')) {
                $table->dropColumn('twitter');
            }
            if (Schema::hasColumn('users', 'linkedin')) {
                $table->dropColumn('linkedin');
            }
            if (Schema::hasColumn('users', 'avg_rating')) {
                $table->dropColumn('avg_rating');
            }
            if (Schema::hasColumn('users', 'show_email_addresss')) {
                $table->dropColumn('show_email_addresss');
            }
            if (Schema::hasColumn('users', 'show_phone_number')) {
                $table->dropColumn('show_phone_number');
            }
            if (Schema::hasColumn('users', 'show_contact_form')) {
                $table->dropColumn('show_contact_form');
            }
            if (Schema::hasColumn('users', 'photo')) {
                $table->dropColumn('photo');
            }
        });
    }
};
