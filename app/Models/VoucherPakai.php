<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherPakai extends Model
{
    protected $table = 'voucher_pakai';

    protected $fillable = [
        'voucher_id', 'user_id', 'transaksi_id', 'diskon',
    ];

    protected function casts(): array
    {
        return [
            'diskon' => 'integer',
        ];
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}
