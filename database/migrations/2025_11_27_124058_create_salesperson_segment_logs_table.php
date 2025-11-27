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
        Schema::create('salesperson_segment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salesperson_id');
            $table->unsignedBigInteger('segment_id');

            $table->string('action'); // created | updated | deleted
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->unsignedBigInteger('updated_by')->nullable(); // user or system

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
        Schema::dropIfExists('salesperson_segment_logs');
    }
};
