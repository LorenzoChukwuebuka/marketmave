<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->mailData['email'];
        $token = $this->mailData['token'];
        $url = env('APP_URL') . '/verify/' . $email . '/' . $token;

        return $this->view('mails.userverify')
            ->with(['url' => $url]);
    }
}