<?php

namespace App\Filament\Admin\Resources\Outlets;

use App\Filament\Admin\Resources\Outlets\Pages\CreateOutlet;
use App\Filament\Admin\Resources\Outlets\Pages\EditOutlet;
use App\Filament\Admin\Resources\Outlets\Pages\ListOutlets;
use App\Filament\Admin\Resources\Outlets\Schemas\OutletForm;
use App\Filament\Admin\Resources\Outlets\Tables\OutletsTable;
use App\Models\Outlet;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OutletResource extends Resource
{
    protected static ?string $model = Outlet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $navigationLabel = 'Outlet';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return OutletForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OutletsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOutlets::route('/'),
            'create' => CreateOutlet::route('/create'),
            'edit' => EditOutlet::route('/{record}/edit'),
        ];
    }
}
