<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Notifications extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subject, $body, $head, $attachmentPath, $attachmentName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $body, $from = null, $attachmentPath = null, $attachmentName = null)
    {
        $this->queue = 'email';
        $this->subject = $subject;
        $this->body = nl2br($body);
        $this->attachmentPath = $attachmentPath;
        $this->attachmentName = $attachmentName;
    
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

        if ($this->attachmentPath) {
            $message->attach($this->attachmentPath, ['as' => $this->attachmentName ?? 'attachment.xlsx']);
        }

        return $message;    
    }
}
