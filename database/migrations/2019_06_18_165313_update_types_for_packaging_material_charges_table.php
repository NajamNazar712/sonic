<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTypesForPackagingMaterialChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packaging_charges', function (Blueprint $table) {
            $table->dropColumn('sm_flyer');
            $table->dropColumn('md_flyer');
            $table->dropColumn('lg_flyer');
            $table->dropColumn('box_flyer');
            $table->integer('type_id')->after('shipping_mode_id');
            $table->integer('size_id');
            $table->integer('charges');
        });

        Schema::table('pending_packaging_charges', function (Blueprint $table) {
            $table->dropColumn('sm_flyer');
            $table->dropColumn('md_flyer');
            $table->dropColumn('lg_flyer');
            $table->dropColumn('box_flyer');
            $table->integer('type_id')->after('shipping_mode_id');
            $table->integer('size_id');
            $table->integer('charges');
        });

        Schema::table('history_packaging_charges', function (Blueprint $table) {
            $table->dropColumn('sm_flyer');
            $table->dropColumn('md_flyer');
            $table->dropColumn('lg_flyer');
            $table->dropColumn('box_flyer');
            $table->integer('type_id')->after('shipping_mode_id');
            $table->integer('size_id');
            $table->integer('charges');
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
            $table->integer('sm_flyer')->after('shipping_mode_id');
            $table->integer('md_flyer');
            $table->integer('lg_flyer');
            $table->integer('box_flyer');
            $table->dropColumn('type_id');
            $table->dropColumn('size_id');
            $table->dropColumn('charges');
        });
        Schema::table('pending_packaging_charges', function (Blueprint $table) {
            $table->integer('sm_flyer')->after('shipping_mode_id');
            $table->integer('md_flyer');
            $table->integer('lg_flyer');
            $table->integer('box_flyer');
            $table->dropColumn('type_id');
            $table->dropColumn('size_id');
            $table->dropColumn('charges');
        });
        Schema::table('history_packaging_charges', function (Blueprint $table) {
            $table->integer('sm_flyer')->after('shipping_mode_id');
            $table->integer('md_flyer');
            $table->integer('lg_flyer');
            $table->integer('box_flyer');
            $table->dropColumn('type_id');
            $table->dropColumn('size_id');
            $table->dropColumn('charges');
        });
    }
}
