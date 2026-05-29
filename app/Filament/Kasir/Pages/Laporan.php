<?php

namespace App\Filament\Kasir\Pages;

use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use App\Models\User;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class Laporan extends Page
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Laporan';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.kasir.pages.laporan';

    public ?string $startDate = null;
    public ?string $endDate = null;

    public $totalPendapatan = 0;
    public $totalTransaksi = 0;
    public $totalMenuTerjual = 0;
    public $totalPelanggan = 0;
    public $menuTerlaris = [];
    public $transaksiBulanan = [];

    public function mount(): void
    {
        $this->form->fill();
        $this->loadData();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('startDate')
                    ->label('Dari Tanggal')
                    ->native(false)
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->loadData()),
                DatePicker::make('endDate')
                    ->label('Sampai Tanggal')
                    ->native(false)
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->loadData()),
            ])
            ->columns(2);
    }

    public function loadData(): void
    {
        $query = Transaksi::query();
        $detailQuery = DetailTransaksi::query();

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
            $detailQuery->whereHas('transaksi', fn($q) => $q->whereDate('created_at', '>=', $this->startDate));
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
            $detailQuery->whereHas('transaksi', fn($q) => $q->whereDate('created_at', '<=', $this->endDate));
        }

        $this->totalPendapatan = (clone $query)
            ->where('status_pembayaran', 'lunas')
            ->where('status_pesanan', '!=', 'dibatalkan')
            ->sum('total_harga');

        $this->totalTransaksi = (clone $query)
            ->where('status_pesanan', '!=', 'dibatalkan')
            ->count();

        $this->totalMenuTerjual = (clone $detailQuery)
            ->whereHas('transaksi', fn($q) => $q
                ->where('status_pembayaran', 'lunas')
                ->where('status_pesanan', '!=', 'dibatalkan')
            )->sum('jumlah');

        $this->totalPelanggan = User::where('role', 'customer')->count();

        $this->menuTerlaris = DetailTransaksi::selectRaw('menu_id, SUM(jumlah) as total_terjual')
            ->whereHas('transaksi', function ($q) {
                $q->where('status_pembayaran', 'lunas')
                  ->where('status_pesanan', '!=', 'dibatalkan');
                if ($this->startDate) $q->whereDate('created_at', '>=', $this->startDate);
                if ($this->endDate) $q->whereDate('created_at', '<=', $this->endDate);
            })
            ->groupBy('menu_id')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->with('menu')
            ->get()
            ->toArray();

        $this->transaksiBulanan = Transaksi::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bulan, SUM(total_harga) as total")
            ->where('status_pembayaran', 'lunas')
            ->where('status_pesanan', '!=', 'dibatalkan')
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(route('laporan.export')),
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action('exportPdf'),
        ];
    }

    public function exportPdf()
    {
        $pdf = Pdf::loadView('filament.kasir.pages.laporan-pdf', [
            'totalPendapatan' => $this->totalPendapatan,
            'totalTransaksi' => $this->totalTransaksi,
            'totalMenuTerjual' => $this->totalMenuTerjual,
            'totalPelanggan' => $this->totalPelanggan,
            'menuTerlaris' => $this->menuTerlaris,
            'transaksiBulanan' => $this->transaksiBulanan,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'laporan-tenscoffee-' . now()->format('Y-m-d') . '.pdf'
        );
    }
}
