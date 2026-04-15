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
        Schema::create('faf_charges_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->decimal('old_percentage', 10,2)->nullable();
            $table->decimal('new_percentage', 10,2)->nullable();
            $table->tinyInteger('old_status')->default(0);
            $table->tinyInteger('new_status')->default(0);
            $table->integer('changed_by')->index();
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
        Schema::dropIfExists('faf_charges_logs');
    }
};
