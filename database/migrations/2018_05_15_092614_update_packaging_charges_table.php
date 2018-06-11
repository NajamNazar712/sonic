<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePackagingChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packaging_charges', function (Blueprint $table) {
            $table->integer('sm_flyer')->change();
            $table->integer('md_flyer')->change();
            $table->integer('lg_flyer')->change();
            $table->integer('box_flyer')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packaging_charges', function (Blueprint $table) {
            $table->decimal('sm_flyer',8,2);
            $table->decimal('md_flyer',8,2);
            $table->decimal('lg_flyer',8,2);
            $table->decimal('box_flyer',8,2);
        });
    }
}
