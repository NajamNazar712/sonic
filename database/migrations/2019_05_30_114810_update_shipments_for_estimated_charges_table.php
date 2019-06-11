<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentsForEstimatedChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->decimal('nsa_osa_estimated_charges')->nullable();
            $table->tinyInteger('nsa_osa_status')->default(0);
            $table->decimal('nsa_osa_charges')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('nsa_osa_estimated_charges');
            $table->dropColumn('nsa_osa_status');
            $table->dropColumn('nsa_osa_charges');
        });
    }
}
