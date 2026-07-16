<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifikasiPembayaranMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tagihan;
    public $tipe;
    public $alasan;

    public function __construct($tagihan, $tipe, $alasan = null)
    {
        $this->tagihan = $tagihan;
        $this->tipe = $tipe; // 'terima' atau 'tolak'
        $this->alasan = $alasan;
    }

    public function envelope(): Envelope
    {
        $subject = $this->tipe === 'terima' 
            ? 'Pembayaran Kost Disetujui 🎉' 
            : '⚠️ PENTING: Pembayaran Kost Ditolak';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notifikasi_pembayaran',
        );
    }
}