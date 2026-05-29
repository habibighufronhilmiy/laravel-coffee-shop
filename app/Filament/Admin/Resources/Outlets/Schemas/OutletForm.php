<?php

namespace App\Filament\Admin\Resources\Outlets\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OutletForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required()
                    ->maxLength(150),
                TextInput::make('alamat')
                    ->required()
                    ->maxLength(255),
                TextInput::make('latitude')
                    ->required()
                    ->numeric()
                    ->rule('between:-90,90')
                    ->maxLength(20)
                    ->helperText('Contoh: -6.917464'),
                TextInput::make('longitude')
                    ->required()
                    ->numeric()
                    ->rule('between:-180,180')
                    ->maxLength(20)
                    ->helperText('Contoh: 107.619123'),
                TextInput::make('no_telp')
                    ->tel()
                    ->maxLength(20),
                TextInput::make('jam_buka')
                    ->regex('/^([01]\d|2[0-3]):([0-5]\d)$/')
                    ->maxLength(5)
                    ->placeholder('08:00')
                    ->helperText('Format: HH:MM (contoh: 08:00)'),
                TextInput::make('jam_tutup')
                    ->regex('/^([01]\d|2[0-3]):([0-5]\d)$/')
                    ->maxLength(5)
                    ->placeholder('22:00')
                    ->helperText('Format: HH:MM (contoh: 22:00)'),
                FileUpload::make('foto')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048)
                    ->directory('outlets'),
                Toggle::make('aktif')
                    ->default(true),
            ]);
    }
}
