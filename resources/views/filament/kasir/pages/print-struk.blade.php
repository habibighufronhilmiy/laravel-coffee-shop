<x-filament-panels::page>
    <style>
        .receipt-wrapper {
            max-width: 320px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1.5rem;
            font-family: 'Courier New', 'Consolas', monospace;
            font-size: 13px;
            line-height: 1.5;
            color: #000;
        }
        .receipt-wrapper td {
            color: #000;
        }
        .receipt-wrapper th {
            color: #000;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 1px dashed #374151;
            padding-bottom: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .receipt-header h2 {
            font-size: 1.25rem;
            font-weight: bold;
            margin: 0;
        }
        .receipt-header p {
            margin: 0;
            color: #000;
            font-size: 11px;
        }
        .receipt-info {
            border-bottom: 1px dashed #374151;
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .receipt-info table {
            width: 100%;
            font-size: 12px;
        }
        .receipt-info td {
            padding: 1px 0;
            vertical-align: top;
        }
        .receipt-info td:last-child {
            text-align: right;
        }
        .receipt-items {
            border-bottom: 1px dashed #374151;
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .receipt-items table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }
        .receipt-items th {
            text-align: left;
            font-size: 11px;
            padding: 2px 0;
            border-bottom: 1px solid #d1d5db;
        }
        .receipt-items th:last-child,
        .receipt-items td:last-child {
            text-align: right;
        }
        .receipt-items td {
            padding: 3px 0;
            vertical-align: top;
        }
        .receipt-total {
            border-bottom: 1px dashed #374151;
            padding-bottom: 0.5rem;
            margin-bottom: 0.75rem;
        }
        .receipt-total table {
            width: 100%;
            font-size: 12px;
        }
        .receipt-total td {
            padding: 2px 0;
        }
        .receipt-total td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .receipt-total .grand-total td {
            font-size: 15px;
            font-weight: bold;
            padding-top: 4px;
            border-top: 1px solid #374151;
        }
        .receipt-footer {
            text-align: center;
            font-size: 12px;
            color: #000;
        }
        .no-print {
            text-align: center;
            margin-top: 1rem;
        }
        @media print {
            .no-print { display: none !important; }
            .fi-sidebar-nav { display: none !important; }
            .fi-topbar { display: none !important; }
            .fi-sidebar-layout { display: block !important; }
            .fi-sidebar-layout > .fi-sidebar-layout-content { margin-left: 0 !important; max-width: 100% !important; padding: 0 !important; }
            body { background: #fff !important; color: #000 !important; }
            .receipt-wrapper {
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            .receipt-header h2 { font-size: 18px; color: #000 !important; }
            .receipt-header p { font-size: 10px; color: #000 !important; }
            .receipt-info table { font-size: 11px; }
            .receipt-info td { color: #000 !important; }
            .receipt-items table { font-size: 11px; }
            .receipt-items td { color: #000 !important; }
            .receipt-items th { color: #000 !important; }
            .receipt-total td { color: #000 !important; }
            .receipt-total .grand-total td { font-size: 14px; color: #000 !important; }
            .receipt-footer p { color: #000 !important; }
            @page {
                margin: 0;
                size: 80mm auto;
            }
        }
    </style>

    <div class="receipt-wrapper">
        <div class="receipt-header">
            <h2>Tens Coffee</h2>
            <p>Struk Pembelian</p>
        </div>

        <div class="receipt-info">
            <table>
                <tr>
                    <td>No. Invoice</td>
                    <td>{{ $transaksi->invoice ?? '#' . $transaksi->id }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>{{ $transaksi->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Meja</td>
                    <td>{{ $transaksi->no_meja }}</td>
                </tr>
                @if($transaksi->user)
                <tr>
                    <td>Customer</td>
                    <td>{{ $transaksi->user->name }}</td>
                </tr>
                @endif
                @if($transaksi->kasir)
                <tr>
                    <td>Kasir</td>
                    <td>{{ $transaksi->kasir->name }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="receipt-items">
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
                        <td colspan="3" style="text-align:center;color:#9ca3af;">Tidak ada item</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="receipt-total">
            <table>
                <tr>
                    <td>Total Harga</td>
                    <td>Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Pembayaran</td>
                    <td>{{ ucfirst($transaksi->metode_pembayaran) }}</td>
                </tr>
                <tr>
                    <td>Status Bayar</td>
                    <td>{{ $transaksi->status_pembayaran === 'lunas' ? 'Lunas' : 'Belum Dibayar' }}</td>
                </tr>
                <tr class="grand-total">
                    <td>Total</td>
                    <td>Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="receipt-footer">
            <p>Terima Kasih!</p>
            <p>Selamat Menikmati</p>
        </div>
    </div>

    <div class="no-print">
        <x-filament::button
            tag="a"
            href="{{ \App\Filament\Kasir\Resources\Transaksis\TransaksiResource::getUrl('print', ['record' => $transaksi->id]) }}?download=1"
            icon="heroicon-o-arrow-down-tray"
            color="primary">
            Download PDF
        </x-filament::button>
        <x-filament::button
            onclick="window.print()"
            icon="heroicon-o-printer"
            color="success"
            class="ml-2">
            Cetak Struk
        </x-filament::button>
        <x-filament::button
            tag="a"
            href="{{ \App\Filament\Kasir\Resources\Transaksis\TransaksiResource::getUrl('index') }}"
            color="gray"
            class="ml-2">
            Kembali
        </x-filament::button>
    </div>
</x-filament-panels::page>

@push('scripts')
<script>
    window.onload = function() {
        setTimeout(function() { window.print(); }, 500);
    }
</script>
@endpush
