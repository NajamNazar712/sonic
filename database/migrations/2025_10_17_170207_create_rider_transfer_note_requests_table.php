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
        Schema::create('rider_transfer_note_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('hub_id')->index();
            $table->unsignedBigInteger('rider_id')->index();
            $table->unsignedBigInteger('route_id')->nullable();
            $table->unsignedBigInteger('retail_store_id')->index();
            $table->unsignedTinyInteger('retail_store_type')->index();
            $table->unsignedInteger('shipments_count');
            $table->decimal('total_cod_amount', 10, 2)->nullable();
            $table->unsignedTinyInteger('status_id')->default(1);
            $table->unsignedBigInteger('approved_by')->nullable()->index();
            $table->timestamp('approved_at')->nullable();
            $table->tinyInteger('updated_type')->nullable()->comment('1-Admin,2-Rider'); // 1 admin 2 rider
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->tinyInteger('ordering')->default(0);
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
        Schema::dropIfExists('rider_transfer_note_requests');
    }
};
