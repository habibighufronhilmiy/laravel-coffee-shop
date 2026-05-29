<?php

namespace App\Filament\Admin\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('judul')
                    ->required()
                    ->maxLength(150),
                Textarea::make('deskripsi')
                    ->maxLength(255),
                FileUpload::make('gambar')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048)
                    ->directory('banners')
                    ->required(),
                TextInput::make('link')
                    ->url()
                    ->maxLength(255),
                TextInput::make('urutan')
                    ->numeric()
                    ->default(0),
                Toggle::make('aktif')
                    ->default(true),
            ]);
    }
}
