<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportsEmail extends Mailable
{
    use  SerializesModels;

    public $subject, $body, $head;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $body, $from = null)
    {
        $this->subject = $subject;
        $this->body = nl2br($body);

        if (filter_var($from, FILTER_VALIDATE_EMAIL)) {
            $this->from($from, 'TRAX');
        } else {
            $this->from('info@slgtrax.com', 'TRAX');
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
