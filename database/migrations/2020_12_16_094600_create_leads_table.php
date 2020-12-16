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
            $table->integer('lead_id')->index();
            $table->string('contact_person');
            $table->integer('city_id')->index();
            $table->string('phone_number');
            $table->string('email_address');
            $table->timestamp('requested_date');
            $table->integer('sale_person_id')->nullable()->index();
            $table->integer('reference_person_id')->nullable()->index();
            $table->integer('status_id')->index();
            $table->integer('updated_by')->nullable()->index();
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
