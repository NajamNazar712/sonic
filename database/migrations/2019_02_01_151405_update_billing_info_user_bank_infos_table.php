<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBillingInfoUserBankInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_bank_infos', function (Blueprint $table) {
            $table->dropColumn('payment_mode');
            $table->integer('invoicing_cycle_id')->nullable();
            $table->integer('generation_date')->nullable();
            $table->string('billing_person_name')->nullable();
            $table->string('billing_person_phone')->nullable();
            $table->string('billing_person_email')->nullable();
            $table->string('billing_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_bank_infos', function (Blueprint $table) {
            $table->string('payment_mode');
            $table->dropColumn('invoicing_cycle_id');
            $table->dropColumn('generation_date');
            $table->dropColumn('billing_person_name');
            $table->dropColumn('billing_person_phone');
            $table->dropColumn('billing_person_email');
            $table->dropColumn('billing_address');
        });
    }
}
