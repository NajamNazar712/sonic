<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendingCashCollectionAgingReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_cash_collection_aging_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hub_id');
            $table->integer('main_hub_id');
            $table->integer('days');
            $table->timestamp('inserted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pending_cash_collection_aging_reports');
    }
}
