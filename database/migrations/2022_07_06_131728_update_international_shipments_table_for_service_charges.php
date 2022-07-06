<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInternationalShipmentsTableForServiceCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('international_shipments', function (Blueprint $table) {
            $table->decimal('service_charges',8,2)->nullable();
            $table->integer('added_by')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('international_shipments', function (Blueprint $table) {
            $table->dropColumn('service_charges');
            $table->dropColumn('added_by');
        });
    }
}
