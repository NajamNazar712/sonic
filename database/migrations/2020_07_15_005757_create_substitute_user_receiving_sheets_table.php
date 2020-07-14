<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubstituteUserReceivingSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('substitute_user_receiving_sheets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('substitute_user_id');
            $table->integer('receiving_sheet_id');
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
        Schema::dropIfExists('substitute_user_receiving_sheets');
    }
}
