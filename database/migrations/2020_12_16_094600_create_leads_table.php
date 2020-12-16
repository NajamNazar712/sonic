<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lead_id');
            $table->string('contact_person');
            $table->integer('city_id');
            $table->string('phone_number');
            $table->string('email_address');
            $table->timestamp('requested_date');
            $table->integer('sale_person_id')->nullable();
            $table->integer('reference_person_id')->nullable();
            $table->integer('status_id');
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('leads');
    }
}
