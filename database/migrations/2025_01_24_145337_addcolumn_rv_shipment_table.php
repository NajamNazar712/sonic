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
        //
        Schema::table('rv_shipment_tickets', function (Blueprint $table) {
            $table->boolean('halt_shipper')->default(0)->comment('0 stand for manual,1 stand for bot call')->after('is_completed');
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
        Schema::table('rv_shipment_tickets', function (Blueprint $table) {
            $table->dropColumn('halt_shipper');
        });  
    }
};
