<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk {{ $transaksi->invoice ?? '#' . $transaksi->id }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; margin: 0; padding: 20px; color: #111827; background: #ffffff; }
        .header { text-align: center; border-bottom: 1px dashed #111827; padding-bottom: 10px; margin-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; color: #111827; }
        .header p { margin: 2px 0; font-size: 11px; color: #111827; }
        .info { border-bottom: 1px dashed #111827; padding-bottom: 8px; margin-bottom: 8px; }
        .info table { width: 100%; font-size: 11px; }
        .info td { padding: 2px 0; color: #111827; }
        .info td:last-child { text-align: right; }
        .items { border-bottom: 1px dashed #111827; padding-bottom: 8px; margin-bottom: 8px; }
        .items table { width: 100%; font-size: 11px; border-collapse: collapse; }
        .items th { text-align: left; border-bottom: 1px solid #111827; padding: 3px 0; font-size: 10px; color: #111827; }
        .items th:last-child, .items td:last-child { text-align: right; }
        .items td { padding: 3px 0; vertical-align: top; color: #111827; }
        .total { border-bottom: 1px dashed #111827; padding-bottom: 8px; margin-bottom: 10px; }
        .total table { width: 100%; font-size: 11px; }
        .total td { padding: 2px 0; color: #111827; }
        .total td:last-child { text-align: right; font-weight: bold; }
        .grand-total td { font-size: 14px; font-weight: bold; border-top: 1px solid #111827; padding-top: 4px; color: #111827; }
        .footer { text-align: center; font-size: 11px; color: #111827; }
    </style>
</head>
<body>
    <div class="header">
        <h2><img src="{{ public_path('img/logo_tens2.jpg') }}" alt="Tens Coffee" style="height: 20px; vertical-align: middle;"> Tens Coffee</h2>
        <p>Struk Pembelian</p>
    </div>

    <div class="info">
        <table>
            <tr><td>Invoice</td><td>{{ $transaksi->invoice ?? '#' . $transaksi->id }}</td></tr>
            <tr><td>Tanggal</td><td>{{ $transaksi->created_at->format('d/m/Y H:i') }}</td></tr>
            <tr><td>Outlet</td><td>{{ $transaksi->outlet?->nama ?? '-' }}</td></tr>
            <tr><td>Meja</td><td>{{ $transaksi->no_meja }}</td></tr>
            @if($transaksi->user)
                <tr><td>Customer</td><td>{{ $transaksi->user->name }}</td></tr>
            @endif
            @if($transaksi->kasir)
                <tr><td>Kasir</td><td>{{ $transaksi->kasir->name }}</td></tr>
            @endif
        </table>
    </div>

    <div class="items">
        <table>
            <thead>
                <tr><th>Menu</th><th>Qty</th><th>Subtotal</th></tr>
            </thead>
            <tbody>
                @forelse($transaksi->detailTransaksis as $detail)
                <tr>
                    <td>{{ $detail->menu->nama_menu ?? '-' }}</td>
                    <td>{{ $detail->jumlah }}</td>
                    <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align:center;color:#999;">Tidak ada item</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="total">
        <table>
            @if($transaksi->ongkir > 0)
            <tr><td>Ongkos Kirim</td><td>Rp {{ number_format($transaksi->ongkir, 0, ',', '.') }}</td></tr>
            @endif
            <tr><td>Pembayaran</td><td>{{ $transaksi->metode_pembayaran === 'cash' ? 'Cash' : 'Midtrans' }}</td></tr>
            <tr><td>Status</td><td>{{ $transaksi->status_pembayaran === 'lunas' ? 'Lunas' : 'Belum Dibayar' }}</td></tr>
            <tr class="grand-total"><td>Total</td><td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td></tr>
        </table>
    </div>

    <div class="footer">
        <p>Terima Kasih!</p>
        <p>Selamat Menikmati</p>
    </div>
</body>
</html>
