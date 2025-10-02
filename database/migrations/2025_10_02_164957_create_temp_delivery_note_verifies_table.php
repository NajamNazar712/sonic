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
    public function up(): void
    {
        Schema::create('temp_delivery_note_verifies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('rider_id');
            $table->date('request_date'); // today’s date in PK
            $table->string('shipment_set_hash', 64); // sha256 of normalized shipment IDs
            $table->longText('shipment_ids_csv');    // normalized CSV (sorted, unique IDs)
            $table->timestamps();

            $table->unique(['rider_id', 'request_date', 'shipment_set_hash'], 'uniq_rider_date_set');
            // Optional: if you want to later link back to created note, add nullable FK:
            // $table->unsignedBigInteger('created_note_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temp_delivery_note_verifies');
    }
};
