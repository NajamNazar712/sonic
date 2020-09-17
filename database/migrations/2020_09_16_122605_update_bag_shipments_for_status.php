<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBagShipmentsForStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bag_shipments', function (Blueprint $table) {
            $table->integer('status')->default(0)->after('shipment_id');
        });
        Schema::table('bags', function (Blueprint $table) {
            $table->integer('short_received')->after('shipments');
            $table->integer('received_shipments')->after('shipments');
            $table->integer('receiver_id')->nullable();
            $table->timestamp('received_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bag_shipments', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('bags', function (Blueprint $table) {
            $table->dropColumn('short_received');
            $table->dropColumn('received_shipments');
            $table->dropColumn('receiver_id');
            $table->dropColumn('received_at');
        });
    }
}
