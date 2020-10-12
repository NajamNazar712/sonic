<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePendingShipmentsForPaymentsIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_shipments_for_payments', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('pending_shipments_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pending_shipments_for_payments', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['pending_shipments_count']);

        });
    }
}
