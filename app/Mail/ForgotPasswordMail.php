<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $user;
    public $resetUrl;

    /**
     * Create a new message instance.
     *
     * @param string $token
     * @param User $user
     * @return void
     */
    public function __construct(string $token, User $user)
    {
        $this->token = $token;
        $this->user = $user;
        $this->resetUrl = config('app.url') . "/password/reset/{$this->token}?email=" . urlencode($user->email);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.forgot-password')
            ->subject('Reset Your Password')
            ->with([
                'resetUrl' => $this->resetUrl,
                'user' => $this->user,
                'token' => $this->token,
            ]);
    }
}
