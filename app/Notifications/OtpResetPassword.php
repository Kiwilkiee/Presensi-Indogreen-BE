<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OtpResetPassword extends Notification
{
    use Queueable;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Kode OTP Reset Password')
            ->line('Berikut adalah kode OTP untuk mereset password Anda:')
            ->line("**{$this->otp}**")
            ->line('Jangan berikan kode ini kepada siapa pun.')
            ->line('Jika Anda tidak meminta reset password, abaikan email ini.');
    }
}
