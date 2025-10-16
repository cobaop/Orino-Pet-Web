<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReservasiDibatalkan extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reservasi;

    public function __construct($reservasi)
    {
        $this->reservasi = $reservasi;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Reservasi Klinik Anda Ditolak')
            ->greeting('Halo ' . $this->reservasi->hewan->user->name . ',')
            ->line('Mohon maaf, reservasi Anda untuk layanan "' . $this->reservasi->layanan->nama_layanan . '" pada tanggal ' . \Carbon\Carbon::parse($this->reservasi->tanggal_reservasi)->format('d-m-Y') . ' jam ' . $this->reservasi->jam_reservasi . ' telah **DIBATALKAN** oleh admin atau dokter.');

        if (!empty($this->reservasi->alasan_penolakan)) {
            $message->line('Alasan penolakan: "' . $this->reservasi->alasan_penolakan . '"');
        }

        return $message
            ->line('Silakan lakukan reservasi ulang jika diperlukan.')
            ->salutation('Salam hangat, Orino Pet Shop & Vet Clinic');
    }
}
