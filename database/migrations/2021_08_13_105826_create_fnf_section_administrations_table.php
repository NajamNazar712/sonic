<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFnfSectionAdministrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fnf_section_administrations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fnf_id')->index();
            $table->double('auction', 8, 2)->nullable();
            $table->double('shirt', 8, 2)->nullable();
            $table->double('maintenance', 8, 2)->nullable();
            $table->string('comments')->nullable();
            $table->integer('created_by')->nullable()->index();
            $table->integer('status_id')->index();
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
        Schema::dropIfExists('fnf_section_administrations');
    }
}
