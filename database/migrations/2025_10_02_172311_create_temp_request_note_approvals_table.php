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
        Schema::create('temp_request_note_approvals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('request_note_id');
            $table->unique('request_note_id');
            $table->date('request_date'); // “today” (use app timezone)

            $table->timestamps();


        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temp_request_note_approvals');
    }
};
