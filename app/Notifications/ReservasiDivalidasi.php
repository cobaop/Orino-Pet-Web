<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReservasiDivalidasi extends Notification implements ShouldQueue
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
        $jamReservasi = $this->reservasi->jam_reservasi ? ' jam ' . $this->reservasi->jam_reservasi : '';

        return (new MailMessage)
                    ->subject('Reservasi Klinik Anda Telah Divalidasi')
                    ->greeting('Halo ' . $this->reservasi->hewan->user->name . ',')
                    ->line('Reservasi klinik Anda untuk layanan "' . $this->reservasi->layanan->nama_layanan . '" pada tanggal ' . $this->reservasi->tanggal_reservasi->format('d-m-Y') . $jamReservasi . ' telah divalidasi.')
                    ->line('Status reservasi Anda sekarang: PROSES.')
                    ->line('Terima kasih telah menggunakan layanan kami.')
                    ->salutation('Salam hangat, Orino Pet Shop & Vet Clinic');
    }
}
