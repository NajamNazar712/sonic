<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToRetailFranchiseProductPerecntagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_franchise_product_perecntages', function (Blueprint $table) {
            $table->integer('retail_shipping_mode_id')->nullable()->after('updated_by');
            $table->decimal('commission_percentage',8,2)->nullable()->after('retail_shipping_mode_id');
            $table->decimal('withholding_tax_percentage',8,2)->nullable()->after('commission_percentage');
            $table->decimal('deduction_percentage',8,2)->nullable()->after('withholding_tax_percentage');
            $table->string('attachment_1')->nullable()->after('deduction_percentage');
            $table->string('attachment_2')->nullable()->after('attachment_1');
            $table->string('attachment_3')->nullable()->after('attachment_2');
            $table->string('attachment_4')->nullable()->after('attachment_3');
            $table->string('attachment_5')->nullable()->after('attachment_4');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_franchise_product_perecntages', function (Blueprint $table) {
            $table->dropColumn('retail_shipping_mode_id');
            $table->dropColumn('commission_percentage');
            $table->dropColumn('withholding_tax_percentage');
            $table->dropColumn('deduction_percentage');
            $table->dropColumn('attachment_1');
            $table->dropColumn('attachment_2');
            $table->dropColumn('attachment_3');
            $table->dropColumn('attachment_4');
            $table->dropColumn('attachment_5');
        });
    }
}
