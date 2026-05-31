<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice',
        'user_id',
        'kasir_id',
        'outlet_id',
        'no_meja',
        'total_harga',
        'ongkir',
        'diskon_poin',
        'tipe_pemesanan',
        'tipe_pengambilan',
        'metode_pembayaran',
        'status_pembayaran',
        'status_pesanan',
        'alamat_pengiriman',
        'latitude_pengiriman',
        'longitude_pengiriman',
        'waktu_pengiriman_dijadwalkan',
        'nama_kurir',
        'midtrans_snap_token',
    ];

    protected function casts(): array
    {
        return [
            'total_harga' => 'integer',
            'ongkir' => 'integer',
            'diskon_poin' => 'integer',
            'latitude_pengiriman' => 'decimal:7',
            'longitude_pengiriman' => 'decimal:7',
            'waktu_pengiriman_dijadwalkan' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Transaksi $transaksi) {
            if (empty($transaksi->invoice)) {
                $transaksi->invoice = static::generateInvoiceNumber();
            }
        });
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');

        $last = static::whereDate('created_at', today())
            ->whereNotNull('invoice')
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->first();

        $seq = $last ? (int) substr($last->invoice, -4) + 1 : 1;

        return $prefix . '-' . $date . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function voucherPakai()
    {
        return $this->hasOne(VoucherPakai::class);
    }

    public function restorePoin(): void
    {
        if ($this->diskon_poin <= 0) return;
        if (!$this->relationLoaded('user')) $this->load('user');
        if (!$this->user) return;

        $poinToRestore = (int) ($this->diskon_poin / 100);
        if ($poinToRestore <= 0) return;

        $this->user->increment('poin', $poinToRestore);

        LoyaltyPoint::create([
            'user_id' => $this->user_id,
            'points' => $poinToRestore,
            'type' => 'restored',
            'description' => "Poin dikembalikan dari pesanan #{$this->invoice} yang dibatalkan",
            'transaksi_id' => $this->id,
        ]);

        $this->update(['diskon_poin' => 0]);
    }
}
