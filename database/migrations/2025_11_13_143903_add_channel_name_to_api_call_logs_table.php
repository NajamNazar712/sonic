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
        Schema::table('api_call_logs', function (Blueprint $table) {
            // Add column with default value 'zong'
            $table->string('channel_name', 100)
                ->default('zong')
                ->after('payload');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_call_logs', function (Blueprint $table) {
            $table->dropColumn('channel_name');
        });
    }
};
