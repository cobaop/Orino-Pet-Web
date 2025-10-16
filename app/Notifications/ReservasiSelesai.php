<?php

namespace App\Notifications;

use App\Models\ReservasiKlinik;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReservasiSelesai extends Notification
{
    use Queueable;

    public ReservasiKlinik $reservasi;

    public function __construct(ReservasiKlinik $reservasi)
    {
        $this->reservasi = $reservasi;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Reservasi Anda Telah Selesai')
            ->greeting('Halo ' . $this->reservasi->hewan->user->name . ',')
            ->line('Reservasi Anda untuk layanan ' . $this->reservasi->layanan->nama_layanan . ' telah selesai.')
            ->line('Terima kasih telah menggunakan layanan kami.')
            ->salutation('Salam hangat, Orino Pet Shop & Vet Clinic');
    }
}
