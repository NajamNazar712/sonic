<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountReconciliationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_reconciliations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->date('month');
            $table->integer('shipments');
            $table->bigInteger('revenue');
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
        Schema::dropIfExists('account_reconciliations');
    }
}
