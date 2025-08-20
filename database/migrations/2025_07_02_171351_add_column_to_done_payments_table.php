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
        Schema::table('done_payments', function (Blueprint $table) {
            $table->boolean('tax_status')->default(false);
            $table->dateTime('tax_status_updated_at')->nullable();
            $table->integer('tax_status_updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('done_payments', function (Blueprint $table) {
            $table->dropColumn(['tax_status', 'tax_status_updated_at','tax_status_updated_by']);
        });
    }
};
