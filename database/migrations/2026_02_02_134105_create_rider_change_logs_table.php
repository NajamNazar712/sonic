<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_change_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('rider_id')->index();
            $table->integer('changed_by')->nullable()->index();
            $table->json('changes');
            $table->string('screen');
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
        Schema::dropIfExists('rider_change_logs');
    }
};
