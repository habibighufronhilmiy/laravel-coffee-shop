<?php

namespace App\Filament\Kasir\Resources\Transaksis\Pages;

use App\Filament\Kasir\Resources\Transaksis\TransaksiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransaksis extends ListRecords
{
    protected static string $resource = TransaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
