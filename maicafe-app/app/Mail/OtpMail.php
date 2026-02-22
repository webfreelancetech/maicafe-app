<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $type;
    public string $recipientName;

    /**
     * @param string $otp           The plain 6-digit OTP
     * @param string $type          'registration' or 'forgot_password'
     * @param string $recipientName Display name for the greeting
     */
    public function __construct(string $otp, string $type, string $recipientName = 'there')
    {
        $this->otp           = $otp;
        $this->type          = $type;
        $this->recipientName = $recipientName;
    }

    public function build(): self
    {
        $subject = $this->type === 'registration'
            ? 'Verify Your Email — Mai Cafe'
            : 'Password Reset OTP — Mai Cafe';

        return $this->subject($subject)
                    ->view('emails.otp');
    }
}
