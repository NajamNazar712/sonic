<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCityDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('city_deliveries', function (Blueprint $table) {
                $table->dropColumn('id');
                $table->dropColumn('created_at');
                $table->dropColumn('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('city_deliveries', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });
    }
}
