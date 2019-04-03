<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Notifications extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subject, $body;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $body)
    {
        $this->queue = 'email';
        $this->subject = $subject;
        $this->body = nl2br($body);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->view('notifications.email')->with(['body' => $this->body]);
    }
}
