<?php

namespace App\Notifications;

use App\Models\Transaksi;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Transaksi $transaksi,
        public string $message
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Status Pesanan #{$this->transaksi->id}")
            ->greeting("Halo {$notifiable->name}!")
            ->line($this->message)
            ->line("No. Invoice: #{$this->transaksi->id}")
            ->line("Total: Rp " . number_format($this->transaksi->total_harga, 0, ',', '.'))
            ->action('Lihat Pesanan', url('/orders'))
            ->line('Terima kasih telah memesan di Tens Coffee!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'transaksi_id' => $this->transaksi->id,
            'message' => $this->message,
            'status_pesanan' => $this->transaksi->status_pesanan,
            'status_pembayaran' => $this->transaksi->status_pembayaran,
        ];
    }
}
