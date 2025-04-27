<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailBroadcast extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $messageContent;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $messageContent)
    {
        $this->subject = $subject;
        $this->messageContent = $messageContent;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->subject)
        ->html("<p>{$this->messageContent}</p>"); 
    }
}
