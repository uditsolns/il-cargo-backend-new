<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $loginUrl;
    public $supportUrl;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->loginUrl = config('app.url') . '/#/auth/login';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.welcome-user')
            ->subject('Welcome to ' . config('app.name') . '!')
            ->with([
                'user' => $this->user,
                'loginUrl' => $this->loginUrl,
            ]);
    }
}
