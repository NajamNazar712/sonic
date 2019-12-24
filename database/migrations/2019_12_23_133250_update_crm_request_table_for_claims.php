<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrmRequestTableForClaims extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_requests', function (Blueprint $table) {
            $table->string('description', 250)->nullable()->default(null)->change();
            $table->integer('product_cost')->nullable()->default(null);
            $table->string('product_picture')->nullable()->default(null);
            $table->string('invoice_picture')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_requests', function (Blueprint $table) {
            $table->string('description', 250)->change();
            $table->dropColumn('product_cost');
            $table->dropColumn('product_picture');
            $table->dropColumn('invoice_picture');
        });
    }
}
