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
            $table->string('cnic_front', 191)->after('otp_expire_at')->nullable();
            $table->string('cnic_back', 191)->after('cnic_front')->nullable();
        });
    }

    public function down()
    {
        Schema::table('retail_shipper_infos', function (Blueprint $table) {
            $table->dropColumn(['cnic_front', 'cnic_back']);
        });
    }

};
