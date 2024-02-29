<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $firstname;

    public function __construct($otp , $firstname)
    {
        $this->otp = $otp;
        $this->firstname = $firstname;
    }

    public function build()
    {
        return $this->view('emails.otp')
            ->subject('Your OTP for Email Verification')
            ->from('webtoneteam@gmail.com', 'Fitness Tracker');
    }
}
