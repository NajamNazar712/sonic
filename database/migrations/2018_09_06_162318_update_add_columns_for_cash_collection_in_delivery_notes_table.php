<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddColumnsForCashCollectionInDeliveryNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->boolean('cash_collection_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->dropColumn('cash_collection');
        });
    }
}
