<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmployeeAttachmentsForUpdateBit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_attachments', function (Blueprint $table) {
            $table->tinyInteger('attachment_update')->default(0)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_attachments', function (Blueprint $table) {
            $table->dropColumn('attachment_update');
        });
    }
}
