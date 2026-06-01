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
                    ->imageEditorMode(1)
                    ->imageEditorViewportWidth(1200)
                    ->imageEditorViewportHeight(240)
                    ->imageEditorAspectRatioOptions([
                        '5:1' => 'Banner Lebar (5:1)',
                        '4:1' => 'Banner Sedang (4:1)',
                        '3:1' => 'Banner Standar (3:1)',
                    ])
                    ->automaticallyResizeImagesToWidth(1920)
                    ->automaticallyResizeImagesToHeight(null)
                    ->automaticallyResizeImagesMode('contain')
                    ->maxSize(5120)
                    ->directory('banners')
                    ->helperText('Rekomendasi: 1920x384px (5:1), maks 5MB')
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
