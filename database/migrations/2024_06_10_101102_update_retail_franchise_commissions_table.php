<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailFranchiseCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_franchise_commissions', function (Blueprint $table) {
            $table->string('franchise_name')->nullable()->after('franchise_code');
            $table->string('franchise_location')->nullable()->after('franchise_name');
            $table->string('franchise_address')->nullable()->after('franchise_location');
            $table->string('franchise_cnic')->nullable()->after('franchise_code');
            $table->string('franchise_phone')->nullable()->after('franchise_cnic');
            $table->string('retail_shipping_mode_name')->nullable()->after('retail_shipping_mode_id');
            $table->decimal('total_charges', 8,2)->nullable()->after('product_percentage');
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
        Schema::table('retail_franchise_commissions', function (Blueprint $table) {
            $table->dropColumn('franchise_name');
            $table->dropColumn('franchise_location');
            $table->dropColumn('franchise_address');
            $table->dropColumn('franchise_cnic');
            $table->dropColumn('franchise_phone');
            $table->dropColumn('retail_shipping_mode_name');
            $table->dropColumn('total_charges');
            $table->dropColumn('weight_charges');
            $table->integer('month')->nullable()->change();
        });
    }
}
