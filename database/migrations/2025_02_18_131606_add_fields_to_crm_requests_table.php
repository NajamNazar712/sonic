<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('crm_requests', function (Blueprint $table) {
            $table->text('complainant_phone')->nullable();
            $table->tinyInteger('case_nature_complainant')->nullable();
        });
    }

    public function down()
    {
        Schema::table('crm_requests', function (Blueprint $table) {
            $table->dropColumn(['complainant_phone', 'case_nature_complainant']);
        });
    }
};

