<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesDesignationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_designations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('designation')->index();
            $table->string('code');
            $table->integer('status');
            $table->integer('incentive')->nullable();
            $table->DateTime('from_date')->nullable();
            $table->DateTime('to_date')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('sales_designations');
    }
}
