<?php

namespace App\Filament\Kasir\Resources\Transaksis;

use App\Filament\Kasir\Resources\Transaksis\Pages\CreateTransaksi;
use App\Filament\Kasir\Resources\Transaksis\Pages\EditTransaksi;
use App\Filament\Kasir\Resources\Transaksis\Pages\ListTransaksis;
use App\Filament\Kasir\Resources\Transaksis\Pages\PrintStruk;
use App\Filament\Kasir\Resources\Transaksis\Schemas\TransaksiForm;
use App\Filament\Kasir\Resources\Transaksis\Tables\TransaksisTable;
use App\Models\Transaksi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Pesanan';
    protected static ?string $pluralLabel = 'Pesanan';
    protected static ?string $modelLabel = 'Pesanan';
    protected static ?string $slug = 'pesanan';

    public static function form(Schema $schema): Schema
    {
        return TransaksiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransaksisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransaksis::route('/'),
            'create' => CreateTransaksi::route('/create'),
            'edit' => EditTransaksi::route('/{record}/edit'),
            'print' => PrintStruk::route('/{record}/print'),
        ];
    }
}
