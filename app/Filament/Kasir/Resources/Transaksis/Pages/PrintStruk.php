<?php

namespace App\Filament\Kasir\Resources\Transaksis\Pages;

use App\Filament\Kasir\Resources\Transaksis\TransaksiResource;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Pages\Page;
use Illuminate\Http\Response;

class PrintStruk extends Page
{
    protected static string $resource = TransaksiResource::class;

    protected string $view = 'filament.kasir.pages.print-struk';

    public Transaksi $transaksi;

    public function mount(Transaksi $record): void
    {
        $this->transaksi = $record->load(['detailTransaksis.menu', 'user', 'kasir', 'outlet']);
    }

    public function getResponse(): ?Response
    {
        if (! request()->query('download')) {
            return null;
        }

        $pdf = Pdf::loadView('filament.kasir.pages.print-struk-pdf', [
            'transaksi' => $this->transaksi,
        ]);

        return $pdf->stream("struk-{$this->transaksi->invoice}.pdf");
    }
}
