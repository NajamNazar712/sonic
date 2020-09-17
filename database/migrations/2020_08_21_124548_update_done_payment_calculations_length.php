<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDonePaymentCalculationsLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('done_payment_calculations', function (Blueprint $table) {
            $table->decimal('packaging_charges', 20,2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('done_payment_calculations', function (Blueprint $table) {
            $table->decimal('packaging_charges', 8,2)->change();
        });
    }
}
