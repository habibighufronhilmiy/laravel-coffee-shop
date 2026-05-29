<?php

namespace App\Events;

use App\Models\Transaksi;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public Transaksi $transaksi
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('orders.' . $this->transaksi->user_id);
    }

    public function broadcastWith(): array
    {
        return [
            'transaksi_id' => $this->transaksi->id,
            'status_pesanan' => $this->transaksi->status_pesanan,
            'status_pembayaran' => $this->transaksi->status_pembayaran,
        ];
    }
}
