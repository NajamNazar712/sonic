<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInternationalShipmentsForPostalCodeStringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('international_shipments', function (Blueprint $table) {
            $table->string('postal_code')->change();
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
            $table->bigInteger('postal_code')->change();
        });
    }
}
