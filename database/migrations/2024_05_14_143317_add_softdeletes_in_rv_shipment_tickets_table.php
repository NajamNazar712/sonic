<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftdeletesInRvShipmentTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rv_shipment_tickets', function (Blueprint $table) {
            $table->text('delete_reason')->default(null)->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rv_shipment_tickets', function (Blueprint $table) {
            $table->dropColumn('delete_reason');
            $table->dropSoftDeletes();
        });
    }
}
