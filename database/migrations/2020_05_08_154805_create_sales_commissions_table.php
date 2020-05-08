<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipper_id');
            $table->integer('commission_users_count');
            $table->double('commission');
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('sales_commissions');
    }
}
