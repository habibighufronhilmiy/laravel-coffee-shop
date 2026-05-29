<?php

namespace App\Filament\Admin\Resources\Vouchers\Pages;

use App\Filament\Admin\Resources\Vouchers\VoucherResource;
use Filament\Resources\Pages\ListRecords;

class ListVouchers extends ListRecords
{
    protected static string $resource = VoucherResource::class;
}
