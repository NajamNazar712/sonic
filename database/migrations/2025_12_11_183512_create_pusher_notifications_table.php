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
        Schema::create('pusher_notifications', function (Blueprint $table) {
            $table->id();

            // Link to main push_notifications table
            $table->unsignedBigInteger('push_notification_id')->nullable();
            $table->foreign('push_notification_id')
                ->references('id')->on('push_notifications')
                ->onDelete('set null');

            // Notification data
            $table->string('device_token')->nullable();
            $table->string('title')->nullable();
            $table->text('body')->nullable();

            // FCM or service error response (JSON)
            $table->json('payload')->nullable();

            // Always 0 for failed
            $table->tinyInteger('status')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pusher_notifications');
    }
};
