<?php

namespace App\Custom;

use App\Mail\UserVerificationMail;
use Illuminate\Support\Facades\Mail;

class MailSender
{
    public static function sendUserVerificationMail(string $email, string $message, string $token)
    {
        $mailData = ["message" => $message, "token" => $token, "email"=>$email, "subject" => "Verification Mail"];

        Mail::to($email)->send(new UserVerificationMail($mailData));
    }
}