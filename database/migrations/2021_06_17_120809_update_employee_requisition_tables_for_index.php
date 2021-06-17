<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmployeeRequisitionTablesForIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_requisition_replacements', function (Blueprint $table) {
            $table->index('trax_id');
        });
        Schema::table('employee_requisition_status_logs', function (Blueprint $table) {
            $table->index('er_id');
            $table->index('admin_id');
            $table->index('status_id');
        });
        Schema::table('employee_requisition_allowances', function (Blueprint $table) {
            $table->index('er_id');
            $table->index('allowance_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_requisition_replacements', function (Blueprint $table) {
            $table->dropIndex(['trax_id']);
        });

        Schema::table('employee_requisition_status_logs', function (Blueprint $table) {
            $table->dropIndex(['er_id']);
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['status_id']);
        });
        Schema::table('employee_requisition_allowances', function (Blueprint $table) {
            $table->dropIndex(['er_id']);
            $table->dropIndex(['allowance_id']);
        });
    }
}
