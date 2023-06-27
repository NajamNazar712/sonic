<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargoManifestBagRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargo_manifest_bag_remarks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bag_id')->index();
            $table->text('remarks')->nullable();
            $table->integer('added_by')->nullable()->index();
            $table->integer('pieces_count')->nullable();
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
        Schema::dropIfExists('cargo_manifest_bag_remarks');
    }
}
