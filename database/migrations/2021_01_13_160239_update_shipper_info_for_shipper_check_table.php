<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipperInfoForShipperCheckTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_shipper_infos', function (Blueprint $table) {
            $table->integer('bank_id')->nullable()->index();
            $table->string('iban')->nullable();
            $table->string('account_number')->nullable();
            $table->string('cheque_image')->nullable();
            $table->integer('status')->default(1);
            $table->integer('completed_status')->default(0);
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
            $table->dropColumn('bank_id');
            $table->dropColumn('iban');
            $table->dropColumn('account_number');
            $table->dropColumn('cheque_image');
            $table->dropColumn('completed_status');
            $table->dropColumn('status');
        });
    }
}
