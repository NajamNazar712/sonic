<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailUserCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_user_commissions', function (Blueprint $table) {
            $table->string('trax_center_name')->nullable()->after('franchise_code');
            $table->string('trax_center_cnic')->nullable()->after('trax_center_name');
            $table->string('trax_center_phone')->nullable()->after('trax_center_cnic');
            $table->string('franchise_address')->nullable()->after('trax_center_phone');
            $table->string('retail_shipping_mode_name')->nullable()->after('retail_shipping_mode_id');
            $table->decimal('total_charges', 8,2)->nullable()->after('number_of_shipments');
            $table->decimal('weight_charges', 8,2)->nullable()->after('total_charges');
            $table->string('month')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_user_commissions', function (Blueprint $table) {
            $table->dropColumn('trax_center_name');
            $table->dropColumn('trax_center_cnic');
            $table->dropColumn('trax_center_phone');
            $table->dropColumn('franchise_address');
            $table->dropColumn('retail_shipping_mode_name');
            $table->dropColumn('total_charges');
            $table->dropColumn('weight_charges');
        });
    }
}
