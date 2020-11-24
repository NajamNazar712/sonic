<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMasterCargoForOnwardForwardingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_cargoes', function (Blueprint $table) {
            $table->integer('onward_forwarding')->default(0);
            $table->string('cnic')->nullable()->after('phone_number');
            $table->dropColumn('type');
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
            $table->dropColumn('onward_forwarding');
            $table->dropColumn('cnic');
            $table->integer('type');
        });
    }
}
