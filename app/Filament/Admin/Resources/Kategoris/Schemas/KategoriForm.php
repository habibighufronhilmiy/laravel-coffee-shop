<?php

namespace App\Filament\Admin\Resources\Kategoris\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KategoriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_kategori')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),
                TextInput::make('icon')
                    ->maxLength(50)
                    ->helperText('Contoh: coffee, food, drink'),
            ]);
    }
}
