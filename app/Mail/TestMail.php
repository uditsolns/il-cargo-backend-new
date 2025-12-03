<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;


    public function build()
    {
        return $this->subject('Test Email')
            ->view('mail.testMail') // This view will contain the email content
            ->with([
                'body' => 'Test email sent successfully.'
            ]);
    }
}
