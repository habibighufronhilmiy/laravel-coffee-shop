<?php

namespace App\Filament\Admin\Resources\Menus\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('kategori_id')
                    ->relationship('kategori', 'nama_kategori')
                    ->required(),
                TextInput::make('nama_menu')
                    ->required()
                    ->maxLength(150),
                Textarea::make('deskripsi')
                    ->maxLength(500),
                TextInput::make('harga')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp'),
                TextInput::make('stok')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                FileUpload::make('foto_menu')
                    ->image()
                    ->imageEditor()
                    ->imageEditorMode(2)
                    ->maxSize(2048)
                    ->directory('menu-photos')
                    ->visibility('public'),
                Repeater::make('variants')
                    ->relationship()
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Varian')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('harga_tambahan')
                            ->label('Tambahan Harga')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->prefix('Rp'),
                        TextInput::make('stok')
                            ->label('Stok')
                            ->numeric()
                            ->minValue(0)
                            ->nullable()
                            ->placeholder('Gunakan stok menu'),
                    ])
                    ->columns(3)
                    ->addActionLabel('Tambah Varian')
                    ->reorderable(false)
                    ->collapsible(false),
                Repeater::make('optionGroups')
                    ->label('Grup Opsi (Sugar Level, Size, Ice, Add-on)')
                    ->relationship()
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Grup')
                            ->required()
                            ->maxLength(255),
                        Select::make('tipe')
                            ->label('Tipe')
                            ->options([
                                'single' => 'Pilih Satu',
                                'multiple' => 'Multiple (Add-on)',
                            ])
                            ->default('single')
                            ->required(),
                        TextInput::make('urutan')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                        Repeater::make('items')
                            ->label('Opsi')
                            ->relationship()
                            ->schema([
                                TextInput::make('nama')
                                    ->label('Nama')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('harga_tambahan')
                                    ->label('Tambahan Harga')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->prefix('Rp'),
                                TextInput::make('stok')
                                    ->label('Stok')
                                    ->numeric()
                                    ->minValue(0)
                                    ->nullable()
                                    ->placeholder('Tidak terbatas'),
                                Toggle::make('is_default')
                                    ->label('Default')
                                    ->default(false),
                                TextInput::make('urutan')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(2)
                            ->addActionLabel('Tambah Opsi')
                            ->reorderable(true)
                            ->collapsible(false),
                    ])
                    ->addActionLabel('Tambah Grup Opsi')
                    ->reorderable(true)
                    ->collapsible(false),
            ]);
    }
}
