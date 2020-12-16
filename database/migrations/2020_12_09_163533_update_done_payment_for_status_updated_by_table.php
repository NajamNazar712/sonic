<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDonePaymentForStatusUpdatedByTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('done_payments', function (Blueprint $table) {
            $table->timestamp('status_updated_at')->nullable()->index();
            $table->integer('status_updated_by')->nullable()->index();
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
            $table->dropColumn('status_updated_at');
            $table->dropColumn('status_updated_by');
        });
    }
}
