<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk {{ $transaksi->invoice ?? '#' . $transaksi->id }}</title>
</head>
<body style="font-family: Helvetica, Arial, sans-serif; font-size: 12px; margin: 20px; padding: 0; color: #000;">
    <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 10px;">
        <h2 style="margin: 0; font-size: 18px; color: #000;">Tens Coffee</h2>
        <p style="margin: 2px 0; font-size: 11px; color: #000;">Struk Pembelian</p>
    </div>

    <div style="border-bottom: 1px solid #000; padding-bottom: 8px; margin-bottom: 8px;">
        <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
            <tr><td style="padding: 2px 0; color: #000;">Invoice</td><td style="padding: 2px 0; text-align: right; color: #000;">{{ $transaksi->invoice ?? '#' . $transaksi->id }}</td></tr>
            <tr><td style="padding: 2px 0; color: #000;">Tanggal</td><td style="padding: 2px 0; text-align: right; color: #000;">{{ $transaksi->created_at->format('d/m/Y H:i') }}</td></tr>
            <tr><td style="padding: 2px 0; color: #000;">Outlet</td><td style="padding: 2px 0; text-align: right; color: #000;">{{ $transaksi->outlet?->nama ?? '-' }}</td></tr>
            <tr><td style="padding: 2px 0; color: #000;">Meja</td><td style="padding: 2px 0; text-align: right; color: #000;">{{ $transaksi->no_meja }}</td></tr>
            @if($transaksi->user)
            <tr><td style="padding: 2px 0; color: #000;">Customer</td><td style="padding: 2px 0; text-align: right; color: #000;">{{ $transaksi->user->name }}</td></tr>
            @endif
            @if($transaksi->kasir)
            <tr><td style="padding: 2px 0; color: #000;">Kasir</td><td style="padding: 2px 0; text-align: right; color: #000;">{{ $transaksi->kasir->name }}</td></tr>
            @endif
        </table>
    </div>

    <div style="border-bottom: 1px solid #000; padding-bottom: 8px; margin-bottom: 8px;">
        <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #000;">
                    <th style="text-align: left; padding: 3px 0; color: #000;">Menu</th>
                    <th style="text-align: right; padding: 3px 0; color: #000;">Qty</th>
                    <th style="text-align: right; padding: 3px 0; color: #000;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi->detailTransaksis as $detail)
                <tr>
                    <td style="padding: 3px 0; color: #000;">{{ $detail->menu->nama_menu ?? '-' }}</td>
                    <td style="text-align: right; padding: 3px 0; color: #000;">{{ $detail->jumlah }}</td>
                    <td style="text-align: right; padding: 3px 0; color: #000;">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align: center; padding: 3px 0; color: #000;">Tidak ada item</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="border-bottom: 1px solid #000; padding-bottom: 8px; margin-bottom: 10px;">
        <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
            @if($transaksi->ongkir > 0)
            <tr><td style="padding: 2px 0; color: #000;">Ongkos Kirim</td><td style="text-align: right; padding: 2px 0; color: #000;">Rp{{ number_format($transaksi->ongkir, 0, ',', '.') }}</td></tr>
            @endif
            <tr><td style="padding: 2px 0; color: #000;">Pembayaran</td><td style="text-align: right; padding: 2px 0; color: #000;">{{ $transaksi->metode_pembayaran === 'cash' ? 'Cash' : 'Midtrans' }}</td></tr>
            <tr><td style="padding: 2px 0; color: #000;">Status</td><td style="text-align: right; padding: 2px 0; color: #000;">{{ $transaksi->status_pembayaran === 'lunas' ? 'Lunas' : 'Belum Dibayar' }}</td></tr>
            <tr><td style="font-size: 14px; font-weight: bold; border-top: 2px solid #000; padding-top: 4px; color: #000;">Total</td><td style="font-size: 14px; font-weight: bold; text-align: right; border-top: 2px solid #000; padding-top: 4px; color: #000;">Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}</td></tr>
        </table>
    </div>

    <div style="text-align: center; font-size: 12px; color: #000;">
        <p style="margin: 2px 0; color: #000;">Terima Kasih!</p>
        <p style="margin: 2px 0; color: #000;">Selamat Menikmati</p>
    </div>
</body>
</html>
