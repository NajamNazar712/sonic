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
        Schema::create('percentage_on_expected_shipments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index('pos_user_idx');
            $table->decimal('percentage_on_expected_shipments', 5, 2)->default(0);
            $table->integer('percentage_on_expected_shipments_added_by')
                ->nullable()
                ->index('pos_added_by_idx');
            $table->dateTime('percentage_on_expected_shipments_added_at')->nullable();
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
        Schema::dropIfExists('percentage_on_expected_shipments');
    }
};
