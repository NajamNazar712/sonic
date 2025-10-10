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
        Schema::table('retail_shipper_infos', function (Blueprint $table) {
            $table->integer('password_reset_limit')->default(0);
            $table->timestamp('password_reset_at')->nullable();
            $table->string('retail_otp',10)->nullable();
            $table->timestamp('otp_expire_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_shipper_infos', function (Blueprint $table) {
            $table->dropColumn('password_reset_limit');
            $table->dropColumn('password_reset_at');
            $table->dropColumn('retail_otp');
            $table->dropColumn('otp_expire_at');
        });
    }
};
