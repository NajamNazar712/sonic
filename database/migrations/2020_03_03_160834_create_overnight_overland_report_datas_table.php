<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOvernightOverlandReportDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overnight_overland_report_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cargo_id');
            $table->integer('origin_id');
            $table->integer('destination_id');
            $table->integer('total_parcels');
            $table->integer('shipping_mode_id');
            $table->integer('vendor_id');
            $table->timestamp('cargo_created_at');
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('overnight_overland_report_datas');
    }
}
