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
        //
//        Schema::table('history_international_user_rates', function (Blueprint $table) {
//            // 🔁 Rename existing columns
//            $table->renameColumn('margin_1', 'margin_1a');
//            $table->renameColumn('margin_8', 'margin_8a');
//            // ➕ Add new columns
//        });
//
//        Schema::table('history_international_user_rates', function (Blueprint $table) {
//            // ➕ Add new columns
//            $table->decimal('margin_1b', 8, 2)->nullable()->after('margin_1a');
//            $table->decimal('margin_8b', 8, 2)->nullable()->after('margin_8a');
//            $table->decimal('margin_8c', 8, 2)->nullable()->after('margin_8b');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        // Rename columns back to original names
        Schema::table('history_international_user_rates', function (Blueprint $table) {
            $table->renameColumn('margin_1a', 'margin_1');
            $table->renameColumn('margin_8a', 'margin_8');
            $table->dropColumn(['margin_1b', 'margin_8a', 'margin_8c']);
        });
    }
};
