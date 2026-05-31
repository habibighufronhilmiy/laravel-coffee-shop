<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Struk {{ $transaksi->invoice ?? '#' . $transaksi->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', Courier, monospace; font-size: 13px; color: #000; background: #fff; padding: 20px; }
        .receipt { max-width: 320px; margin: 0 auto; }
        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
        .header h2 { font-size: 18px; font-weight: bold; color: #000; }
        .header p { font-size: 11px; color: #000; margin-top: 2px; }
        .info { border-bottom: 1px dashed #000; padding-bottom: 8px; margin-bottom: 8px; }
        .info table { width: 100%; font-size: 12px; border-collapse: collapse; }
        .info td { padding: 2px 0; color: #000; vertical-align: top; }
        .info td:last-child { text-align: right; }
        .items { border-bottom: 1px dashed #000; padding-bottom: 8px; margin-bottom: 8px; }
        .items table { width: 100%; font-size: 12px; border-collapse: collapse; }
        .items th { text-align: left; font-size: 11px; padding: 3px 0; border-bottom: 1px solid #000; color: #000; }
        .items th:last-child, .items td:last-child { text-align: right; }
        .items td { padding: 3px 0; color: #000; vertical-align: top; }
        .total { border-bottom: 1px dashed #000; padding-bottom: 8px; margin-bottom: 10px; }
        .total table { width: 100%; font-size: 12px; border-collapse: collapse; }
        .total td { padding: 2px 0; color: #000; }
        .total td:last-child { text-align: right; font-weight: bold; }
        .grand-total td { font-size: 15px; font-weight: bold; padding-top: 4px; border-top: 2px solid #000; }
        .footer { text-align: center; font-size: 12px; color: #000; }
        .footer p { margin: 2px 0; color: #000; }
        @media print {
            body { padding: 0; }
            @page { margin: 0; size: 80mm auto; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h2>Tens Coffee</h2>
            <p>Struk Pembelian</p>
        </div>

        <div class="info">
            <table>
                <tr><td>No. Invoice</td><td>{{ $transaksi->invoice ?? '#' . $transaksi->id }}</td></tr>
                <tr><td>Tanggal</td><td>{{ $transaksi->created_at->format('d/m/Y H:i') }}</td></tr>
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
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi->detailTransaksis as $detail)
                    <tr>
                        <td>{{ $detail->menu->nama_menu ?? '-' }}</td>
                        <td>{{ $detail->jumlah }}</td>
                        <td>Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center;color:#000;">Tidak ada item</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="total">
            <table>
                <tr><td>Total Harga</td><td>Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}</td></tr>
                <tr><td>Pembayaran</td><td>{{ ucfirst($transaksi->metode_pembayaran) }}</td></tr>
                <tr><td>Status Bayar</td><td>{{ $transaksi->status_pembayaran === 'lunas' ? 'Lunas' : 'Belum Dibayar' }}</td></tr>
                <tr class="grand-total">
                    <td>Total</td>
                    <td>Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Terima Kasih!</p>
            <p>Selamat Menikmati</p>
        </div>
    </div>

    <div class="no-print" style="text-align:center;margin-top:20px;padding-top:10px;border-top:1px dashed #000;">
        <button onclick="window.print()" style="padding:8px 24px;font-size:14px;cursor:pointer;background:#000;color:#fff;border:none;border-radius:4px;">Cetak Struk</button>
    </div>
</body>
</html>
