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
        Schema::create('local_trip_vehicle_costs', function (Blueprint $table) {
            $table->id();
            $table->integer('trip_id')->index();
            $table->decimal('cost_amount', 12, 2);
//            $table->enum('cost_type', ['Fuel', 'Misc'])->default('Misc');
            $table->text('remarks')->nullable();
            $table->string('receipt_path')->nullable();
            $table->integer('created_by')->index()->nullable();
            $table->integer('updated_by')->index()->nullable();
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
        Schema::dropIfExists('local_trip_vehicle_costs');
    }
};
