<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Notifications extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subject, $body, $head;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $body, $from = null)
    {
        $this->queue = 'email';
        $this->subject = $subject;
        $this->body = nl2br($body);

    
        if ($from) {
            $this->from($from, 'TRAX');
        }
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $message = $this->subject($this->subject)
        ->view('notifications.email')
        ->with(['body' => $this->body]);

        return $message;    
    }
}
