<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentTableWalkInStatusAndUpdtedBy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('shipments', function (Blueprint $table) {
            $table->integer('walk_in_delivery_type_id')->nullable();
            $table->integer('walk_in_status')->default(0)->nullable();
            $table->integer('charges_mode_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('walk_in_delivery_type');
            $table->dropColumn('walk_in_status');
            $table->dropColumn('charges_mode_id');
        });
    }
}
