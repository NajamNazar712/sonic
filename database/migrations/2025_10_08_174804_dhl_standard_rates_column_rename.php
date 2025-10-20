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
    public function up(): void
    {
        Schema::table('international_standard_dhl_rates', function (Blueprint $table) {
            // 🔁 Rename existing columns
            $table->renameColumn('zone_1', 'zone_1a');
            $table->renameColumn('zone_8', 'zone_8a');
            // ➕ Add new columns  
        });
        
        Schema::table('international_standard_dhl_rates', function (Blueprint $table) {
            // ➕ Add new columns  
            $table->decimal('zone_1b', 8, 2)->nullable()->after('zone_1a');
            $table->decimal('zone_8b', 8, 2)->nullable()->after('zone_8a');
            $table->decimal('zone_8c', 8, 2)->nullable()->after('zone_8b');
        });

        Schema::table('intl_economy_standard_retail_rates', function (Blueprint $table) {
            // 🔁 Rename existing columns
            $table->renameColumn('zone_1', 'zone_1a');
            $table->renameColumn('zone_8', 'zone_8a');
            // ➕ Add new columns  
        });

        DB::statement('ALTER TABLE intl_economy_standard_retail_rates MODIFY zone_8b DECIMAL(8,2) NULL AFTER zone_8a');

        // Move "zone_8B" after "zone_8A"

        // Move "zone_8A" after "zone_7"
        DB::statement('ALTER TABLE intl_economy_standard_retail_rates MODIFY zone_1b DECIMAL(8,2) NULL AFTER zone_1a');

        Schema::table('intl_economy_standard_retail_rates', function (Blueprint $table) {
            // ➕ Add new columns  
            $table->decimal('zone_8c', 8, 2)->nullable()->after('zone_8b');
        });
        // Example of reordering column "zone_8C" to come after "zone_8B"

        Schema::table('international_standard_retail_rates', function (Blueprint $table) {
            // 🔁 Rename existing columns
            $table->renameColumn('zone_1', 'zone_1a');
            $table->renameColumn('zone_8', 'zone_8a');
            // ➕ Add new columns  
        });

        DB::statement('ALTER TABLE international_standard_retail_rates MODIFY zone_8b DECIMAL(8,2) NULL AFTER zone_8a');

        // Move "zone_8B" after "zone_8A"

        // Move "zone_8A" after "zone_7"
        DB::statement('ALTER TABLE international_standard_retail_rates MODIFY zone_1b DECIMAL(8,2) NULL AFTER zone_1a');

        Schema::table('international_standard_retail_rates', function (Blueprint $table) {
            // ➕ Add new columns  
            $table->decimal('zone_8c', 8, 2)->nullable()->after('zone_8b');
        });
        // Example of reordering column "zone_8C" to come after "zone_8B"

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('international_standard_dhl_rates', function (Blueprint $table) {
            // Revert renames
            $table->renameColumn('zone_1a', 'zone_1');
            $table->renameColumn('zone_8a', 'zone_8');

            // Drop new columns
            $table->dropColumn(['zone_1b','zone_8b','zone_8c']);
        });

        Schema::table('intl_economy_standard_retail_rates', function (Blueprint $table) {
            // Revert renames
            $table->renameColumn('zone_1a', 'zone_1');
            $table->renameColumn('zone_8a', 'zone_8');

            // Drop new columns
            $table->dropColumn(['zone_1b','zone_8b','zone_8c']);
        });

        Schema::table('international_standard_retail_rates', function (Blueprint $table) {
            // Revert renames
            $table->renameColumn('zone_1a', 'zone_1');
            $table->renameColumn('zone_8a', 'zone_8');

            // Drop new columns
            $table->dropColumn(['zone_1b','zone_8b','zone_8c']);
        });
    }
};
