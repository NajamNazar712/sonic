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
        Schema::table('international_user_rates', function (Blueprint $table) {
            // 🔁 Rename existing columns
            $table->renameColumn('margin_1', 'margin_1a');
            $table->renameColumn('margin_8', 'margin_8a');
            // ➕ Add new columns  
        });

        Schema::table('international_user_rates', function (Blueprint $table) {
            // ➕ Add new columns  
            $table->decimal('margin_1b', 8, 2)->nullable()->after('margin_1a');
            $table->decimal('margin_8b', 8, 2)->nullable()->after('margin_8a');
            $table->decimal('margin_8c', 8, 2)->nullable()->after('margin_8b');
        });

        Schema::table('pending_international_user_rates', function (Blueprint $table) {
            // 🔁 Rename existing columns
            $table->renameColumn('margin_1', 'margin_1a');
            $table->renameColumn('margin_8', 'margin_8a');
            // ➕ Add new columns  
        });

        DB::statement('ALTER TABLE pending_international_user_rates MODIFY margin_8b DECIMAL(8,2) NULL AFTER margin_8a');

        // Move "margin_8B" after "margin_8A"

        // Move "margin_8A" after "margin_7"
        DB::statement('ALTER TABLE pending_international_user_rates MODIFY margin_1b DECIMAL(8,2) NULL AFTER margin_1a');

        Schema::table('pending_international_user_rates', function (Blueprint $table) {
            // ➕ Add new columns  
            $table->decimal('margin_8c', 8, 2)->nullable()->after('margin_8b');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('international_user_rates', function (Blueprint $table) {
            // Revert renames
            $table->renameColumn('margin_1a', 'margin_1');
            $table->renameColumn('margin_8a', 'margin_8');

            // Drop new columns
            $table->dropColumn(['margin_1b', 'margin_8a', 'margin_8c']);
        });
        // Reverse order: drop new columns first
        Schema::table('pending_international_user_rates', function (Blueprint $table) {
            $table->dropColumn(['margin_1b', 'margin_8b', 'margin_8c']);
        });

        // Rename columns back to original names
        Schema::table('pending_international_user_rates', function (Blueprint $table) {
            $table->renameColumn('margin_1a', 'margin_1');
            $table->renameColumn('margin_8a', 'margin_8');
        });
    }

    
};
