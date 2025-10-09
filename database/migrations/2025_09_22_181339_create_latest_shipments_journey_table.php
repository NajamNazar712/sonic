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
        Schema::create('latest_shipments_journey', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('shipment_id')->unique(); // one latest per shipment
            $table->unsignedInteger('shipper_status_id')->nullable()->index();
            $table->unsignedInteger('consignee_status_id')->nullable()->index();
            $table->unsignedInteger('status_reason_id')->nullable()->index();
            $table->text('remarks')->nullable();

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('admin_id')->nullable()->index();
            $table->unsignedBigInteger('rider_id')->nullable()->index();
            $table->unsignedBigInteger('city_id')->nullable()->index();

            $table->unsignedBigInteger('reference_1_id')->nullable()->index();
            $table->unsignedBigInteger('reference_2_id')->nullable()->index();

            $table->string('received_or_refused_by', 191)->nullable();
            $table->boolean('verification')->default(1)->index();
            $table->string('ip_address', 191)->nullable();
            $table->string('relation', 191)->nullable();
            $table->string('cnic', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('latest_shipments_journey');
    }
};
