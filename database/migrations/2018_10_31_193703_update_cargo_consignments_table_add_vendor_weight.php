<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCargoConsignmentsTableAddVendorWeight extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_consignments', function (Blueprint $table) {
            $table->decimal('vendor_weight', 16, 2)->after('actual_weight')->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cargo_consignments', function (Blueprint $table) {
            $table->dropColumn('vendor_weight');
        });
    }
}
