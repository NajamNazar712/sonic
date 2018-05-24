<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInsuranceChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('insurance_charges', function (Blueprint $table) {
            $table->integer('range_up')->change();
            $table->integer('range_down')->change();
            $table->string('charges')->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('insurance_charges', function (Blueprint $table) {
            $table->decimal('range_up');
            $table->decimal('range_down');
            $table->decimal('charges');
        });
    }
}
