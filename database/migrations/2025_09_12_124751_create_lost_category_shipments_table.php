<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lost_category_shipments', function (Blueprint $table) {
            $table->id();
            $table->integer('shipment_id')->index();

            $table->enum('type', [
                'Transit Lost',
                'Snatching/Theft/Stolen',
                'Lost by Operation Staff',
                'Lost by Rider',
                'Lost by Rider & Operation Staff'
            ]);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lost_category_shipments');
    }
};
