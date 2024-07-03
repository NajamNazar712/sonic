<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFafChargesGlobalHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faf_charges_globals', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('faf_charges',10,2);
            $table->integer('admin_id')->index();
            $table->date('date_range_start');
            $table->date('date_range_end');
            $table->timestamps();
        });

        Schema::create('faf_charges_global_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('faf_charges',10,2);
            $table->integer('admin_id')->index();
            $table->date('date_range_start');
            $table->date('date_range_end');
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
        Schema::dropIfExists('faf_charges_globals');
        Schema::dropIfExists('faf_charges_global_histories');
    }
}
