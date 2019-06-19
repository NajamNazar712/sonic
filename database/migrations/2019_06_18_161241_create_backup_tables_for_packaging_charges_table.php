<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBackupTablesForPackagingChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('backup_packaging_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('shipping_mode_id');
            $table->decimal('sm_flyer',8,2);
            $table->decimal('md_flyer',8,2);
            $table->decimal('lg_flyer',8,2);
            $table->decimal('box_flyer',8,2);
            $table->timestamps();
        });
        Schema::create('backup_history_packaging_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('shipping_mode_id');
            $table->decimal('sm_flyer',8,2);
            $table->decimal('md_flyer',8,2);
            $table->decimal('lg_flyer',8,2);
            $table->decimal('box_flyer',8,2);
            $table->timestamps();
        });
        Schema::create('backup_pending_packaging_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('shipping_mode_id');
            $table->decimal('sm_flyer',8,2);
            $table->decimal('md_flyer',8,2);
            $table->decimal('lg_flyer',8,2);
            $table->decimal('box_flyer',8,2);
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
        Schema::dropIfExists('backup_packaging_charges');
        Schema::dropIfExists('backup_history_packaging_charges');
        Schema::dropIfExists('backup_pending_packaging_charges');
    }
}
