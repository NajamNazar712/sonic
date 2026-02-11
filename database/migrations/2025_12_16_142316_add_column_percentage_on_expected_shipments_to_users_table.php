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
        // Schema::table('users', function (Blueprint $table) {
        //     $table->decimal('percentage_on_expected_shipments', 5, 2)->default(0);
        //     $table->integer('percentage_on_expected_shipments_added_by')->nullable()->index();
        //     $table->dateTime('percentage_on_expected_shipments_added_at')->nullable();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropColumn(['percentage_on_expected_shipment','percentage_on_expected_shipments_added_by', 'percentage_on_expected_shipments_added_at']);
        // });
    }
};
