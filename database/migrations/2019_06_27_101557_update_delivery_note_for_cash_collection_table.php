<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDeliveryNoteForCashCollectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->integer('cash_collected_by')->nullable();
            $table->timestamp('cash_collected_at')->nullable();
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
            $table->dropColumn('cash_collected_by');
            $table->dropColumn('cash_collected_at');
        });
    }
}
