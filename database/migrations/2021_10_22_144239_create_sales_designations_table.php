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
            $table->integer('status')->index();
            $table->decimal('incentive')->nullable();
            $table->integer('created_by')->index();
            $table->integer('updated_by')->index()->nullable();
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
