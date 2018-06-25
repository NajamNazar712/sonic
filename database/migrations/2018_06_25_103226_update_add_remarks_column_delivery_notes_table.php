<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddRemarksColumnDeliveryNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->bigInteger('expense')->after('received_cod_amount')->nullable();
            $table->bigInteger('net_amount')->after('expense')->nullable();
            $table->string('remarks')->after('net_amount')->nullable();
            $table->boolean('dncc_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->dropColumn('net_amount');
            $table->dropColumn('expense');
            $table->dropColumn('remarks');
            $table->dropColumn('dncc_status');
        });
    }
}
