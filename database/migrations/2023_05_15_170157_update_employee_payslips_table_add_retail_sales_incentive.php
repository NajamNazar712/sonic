    <?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmployeePayslipsTableAddRetailSalesIncentive extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_payslips', function (Blueprint $table) {
            $table->integer('retail_incentive')->nullable();
            $table->integer('sales_incentive')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_payslips', function (Blueprint $table) {
            $table->dropColumn('retail_incentive');
            $table->dropColumn('sales_incentive');
        });
    }
}
