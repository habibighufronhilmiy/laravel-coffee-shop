<?php

namespace App\Filament\Admin\Resources\Transaksis;

use App\Filament\Admin\Resources\Transaksis\Pages\ListTransaksis;
use App\Filament\Admin\Resources\Transaksis\Tables\TransaksisTable;
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

    protected static ?string $navigationLabel = 'Transaksi';
    protected static ?string $pluralLabel = 'Transaksi';
    protected static ?string $modelLabel = 'Transaksi';
    protected static ?string $slug = 'transaksi';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return TransaksisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransaksis::route('/'),
        ];
    }
}
