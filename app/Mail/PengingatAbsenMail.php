<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PengingatAbsenMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pesan, $user;

    public function __construct($user, $pesan)
    {
        $this->user = $user;
        $this->pesan = $pesan;
    }

    public function build()
    {
        return $this->subject('Pengingat Absen Masuk')
        ->view('emails.pengingat_absen')
        ->with([
            'nama' => $this->user->nama,
            'pesan' => $this->pesan,
        ]);
    }
}
