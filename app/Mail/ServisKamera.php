<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServisKamera extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->subject = 'Verifikasi Email Anda untuk pendaftaran Jakarta Camera';
    }

    public function build()
    {
        return $this->view('emails.servis-kamera')
            ->with('data', $this->data)
            ->subject($this->subject);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
