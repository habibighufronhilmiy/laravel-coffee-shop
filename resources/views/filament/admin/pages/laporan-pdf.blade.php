<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tens Coffee</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; margin: 0; padding: 30px; font-size: 12px; color: #333; }
        h1 { text-align: center; margin: 0 0 5px; font-size: 20px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 20px; font-size: 11px; }
        .ringkasan { margin-bottom: 20px; }
        .ringkasan table { width: 100%; border-collapse: collapse; }
        .ringkasan td { padding: 6px 10px; border: 1px solid #ddd; }
        .ringkasan td:first-child { font-weight: bold; width: 50%; }
        .ringkasan td:last-child { text-align: right; }
        h2 { font-size: 14px; border-bottom: 2px solid #333; padding-bottom: 3px; margin: 20px 0 10px; }
        table.items { width: 100%; border-collapse: collapse; }
        table.items th { background: #f5f5f5; padding: 6px 10px; text-align: left; font-size: 11px; border: 1px solid #ddd; }
        table.items td { padding: 5px 10px; border: 1px solid #ddd; }
        table.items th:last-child, table.items td:last-child { text-align: right; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <h1><img src="{{ public_path('img/logo_tens2.jpg') }}" alt="Tens Coffee" style="height: 24px; vertical-align: middle;"> Laporan Tens Coffee</h1>
    <div class="subtitle">
        {{ now()->format('d/m/Y H:i') }}
        @if($startDate || $endDate)
            <br>Periode:
            {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }}
            -
            {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Sekarang' }}
        @endif
    </div>

    <h2>Ringkasan</h2>
    <div class="ringkasan">
        <table>
            <tr><td>Total Pendapatan</td><td>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td></tr>
            <tr><td>Total Transaksi</td><td>{{ $totalTransaksi }}</td></tr>
            <tr><td>Total Menu Terjual</td><td>{{ $totalMenuTerjual }}</td></tr>
            <tr><td>Total Pelanggan</td><td>{{ $totalPelanggan }}</td></tr>
        </table>
    </div>

    <h2>Menu Terlaris</h2>
    <table class="items">
        <thead>
            <tr><th>Menu</th><th>Terjual</th></tr>
        </thead>
        <tbody>
            @forelse($menuTerlaris as $item)
            <tr><td>{{ $item['menu']['nama_menu'] ?? '-' }}</td><td>{{ $item['total_terjual'] }}</td></tr>
            @empty
            <tr><td colspan="2" style="text-align:center;color:#999;">Belum ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Pendapatan Bulanan</h2>
    <table class="items">
        <thead>
            <tr><th>Bulan</th><th>Total</th></tr>
        </thead>
        <tbody>
            @forelse($transaksiBulanan as $item)
            <tr>
                <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $item['bulan'])->locale('id')->translatedFormat('F Y') }}</td>
                <td>Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="2" style="text-align:center;color:#999;">Belum ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
