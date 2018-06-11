<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePickupsJourneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('pickups_journey', 'pickup_notes_journey');

        Schema::table('pickup_notes_journey', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->text('remarks')->nullable(FALSE)->change();
            $table->integer('admin_id')->nullable(FALSE)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pickup_notes_journey', function (Blueprint $table) {
            $table->integer('user_id')->nullable()->default(NULL)->after('remarks');
        });

        Schema::rename('pickup_notes_journey', 'pickups_journey');
    }
}
