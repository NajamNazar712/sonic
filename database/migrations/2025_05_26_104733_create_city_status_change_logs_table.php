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
        Schema::create('city_status_change_logs', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('column_type'); // 1 = Booking, 2 = City
            $table->integer('updated_by')->index();  
            $table->integer('city_id')->index();      
            $table->boolean('new_status');              // true = enabled, false = disabled
            $table->timestamp('changed_at');        
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
        Schema::dropIfExists('city_status_change_logs');
    }
};
