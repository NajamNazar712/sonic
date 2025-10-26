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
        Schema::create('return_transfer_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('hub_id')->index();
            $table->unsignedBigInteger('rider_id')->index();
            $table->unsignedBigInteger('route_id')->nullable();
            $table->unsignedBigInteger('retail_store_id')->index();
            $table->unsignedTinyInteger('retail_store_type')->index();
            $table->unsignedInteger('shipments_count');
            $table->unsignedInteger('completed_shipments')->default(0);
            $table->decimal('total_cod_amount', 10, 2)->nullable();
            $table->unsignedBigInteger('admin_id')->index();
            $table->unsignedTinyInteger('status')->default(1)->index();
            $table->unsignedBigInteger('last_status_updated_by')->nullable();
            $table->timestamp('last_status_updated_at')->nullable();
            $table->tinyInteger('last_status_updated_type')->nullable()->comment('1-Admin,2-Rider,3-Retail');
            $table->tinyInteger('created_via_app')->default(0);
            $table->tinyInteger('ordering')->default(0);
            $table->unsignedBigInteger('request_note_id')->index();
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
        Schema::dropIfExists('return_transfer_notes');
    }
};
