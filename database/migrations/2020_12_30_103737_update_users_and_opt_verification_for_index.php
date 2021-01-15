<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersAndOptVerificationForIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('phone_number_verified');
        });

        Schema::table('user_otp_verifications', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('otp');
        });

        Schema::table('month_closings', function (Blueprint $table) {
            $table->index('status_id');
            $table->index('closing_date');
            $table->index('closing_type_id');
            $table->index('created_at');
            $table->index('updated_at');
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
            $table->dropIndex(['phone_number_verified']);
        });

        Schema::table('user_otp_verifications', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['otp']);
        });

        Schema::table('month_closings', function (Blueprint $table) {
            $table->dropIndex(['status_id']);
            $table->dropIndex(['closing_date']);
            $table->dropIndex(['closing_type_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });
    }
}
