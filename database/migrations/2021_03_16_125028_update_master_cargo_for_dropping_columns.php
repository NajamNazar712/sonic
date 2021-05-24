<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMasterCargoForDroppingColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_cargoes', function (Blueprint $table) {
            $table->dropColumn('builty_number');
            $table->dropColumn('cnic');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_cargoes', function (Blueprint $table) {
            $table->string('builty_number');
            $table->string('cnic');
        });
    }
}
