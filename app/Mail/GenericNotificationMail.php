<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $title;
    public string $messageBody;

    /**
     * Create a new message instance.
     */
    public function __construct(string $title, string $messageBody)
    {
        $this->title = $title;
        $this->messageBody = $messageBody;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->title)
            ->view('emails.generic-notification')
            ->with([
                'title' => $this->title,
                'messageBody' => $this->messageBody,
            ]);
    }
}

