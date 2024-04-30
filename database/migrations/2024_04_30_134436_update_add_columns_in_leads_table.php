<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddColumnsInLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->integer('average_shipment_per_week')->after('call_status');
            $table->integer('average_parcel_cod_amount')->after('average_shipment_per_week');
            $table->string('business_address')->after('average_parcel_cod_amount');
            $table->string('company_name')->after('business_address');
            $table->boolean('business_registered_status')->after('company_name');
            $table->integer('ntn_number')->after('business_registered_status');
            $table->integer('activation_code')->after('ntn_number');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('average_shipment_per_week');
            $table->dropColumn('average_parcel_cod_amount');
            $table->dropColumn('business_address');
            $table->dropColumn('company_name');
            $table->dropColumn('business_registered_status');
            $table->dropColumn('ntn_number');
            $table->dropColumn('activation_code');
        });
    }
}
