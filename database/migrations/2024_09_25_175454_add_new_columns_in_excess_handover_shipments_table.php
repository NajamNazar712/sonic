<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsInExcessHandoverShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('excess_handover_shipments', function (Blueprint $table) {
            $table->unsignedBigInteger('excess_handover_id')->nullable()->after('handover_id');
            $table->unsignedBigInteger('excess_bag_number')->nullable()->after('bag_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('excess_handover_shipments', function (Blueprint $table) {
            $table->dropColumn('excess_handover_id');
            $table->dropColumn('excess_bag_number');
        });
    }
}
