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
        Schema::table('international_standard_dhl_rates', function (Blueprint $table) {
            $table->renameColumn('zone_1a', 'zone_1');
            $table->renameColumn('zone_8a', 'zone_8');
        });

        Schema::table('intl_economy_standard_retail_rates', function (Blueprint $table) {
            $table->renameColumn('zone_1a', 'zone_1');
            $table->renameColumn('zone_8a', 'zone_8');
        });

        Schema::table('intl_economy_standard_retail_rates', function (Blueprint $table) {
            $table->decimal('zone_12', 8, 2)->nullable()->after('zone_11');
            $table->decimal('zone_13', 8, 2)->nullable()->after('zone_12');
        });

        Schema::table('international_standard_retail_rates', function (Blueprint $table) {
            $table->renameColumn('zone_1a', 'zone_1');
            $table->renameColumn('zone_8a', 'zone_8');
        });
        Schema::table('international_standard_retail_rates', function (Blueprint $table) {
            $table->decimal('zone_12', 8, 2)->nullable()->after('zone_11');
            $table->decimal('zone_13', 8, 2)->nullable()->after('zone_12');
        });
        Schema::table('international_user_rates', function (Blueprint $table) {
            $table->renameColumn('margin_1a', 'margin_1');
            $table->renameColumn('margin_8a', 'margin_8');
        });

        Schema::table('pending_international_user_rates', function (Blueprint $table) {
            $table->renameColumn('margin_1a', 'margin_1');
            $table->renameColumn('margin_8a', 'margin_8');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('international_standard_dhl_rates', function (Blueprint $table) {
            $table->renameColumn('zone_1', 'zone_1a');
            $table->renameColumn('zone_8', 'zone_8a');
        });

        Schema::table('intl_economy_standard_retail_rates', function (Blueprint $table) {
            $table->dropColumn(['zone_12', 'zone_13']);
        });
        Schema::table('international_standard_retail_rates', function (Blueprint $table) {
            $table->dropColumn(['zone_12', 'zone_13']);
        });

        Schema::table('intl_economy_standard_retail_rates', function (Blueprint $table) {
            $table->renameColumn('zone_1', 'zone_1a');
            $table->renameColumn('zone_8', 'zone_8a');
        });

        Schema::table('international_standard_retail_rates', function (Blueprint $table) {
            $table->renameColumn('zone_1', 'zone_1a');
            $table->renameColumn('zone_8', 'zone_8a');
        });

        Schema::table('international_user_rates', function (Blueprint $table) {
            $table->renameColumn('margin_1', 'margin_1a');
            $table->renameColumn('margin_8', 'margin_8a');
        });

        Schema::table('pending_international_user_rates', function (Blueprint $table) {
            $table->renameColumn('margin_1', 'margin_1a');
            $table->renameColumn('margin_8', 'margin_8a');
        });
    }
};
