<?php

namespace App\Filament\Admin\Resources\Menus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                ImageColumn::make('foto_menu')->circular()->defaultImageUrl(fn() => 'data:image/svg+xml,...'),
                TextColumn::make('nama_menu')->searchable()->sortable(),
                TextColumn::make('kategori.nama_kategori')->label('Kategori')->sortable(),
                TextColumn::make('harga')->money('idr')->sortable(),
                TextColumn::make('stok')
                    ->sortable()
                    ->color(fn(int $state): string => $state < 5 ? 'danger' : ($state < 10 ? 'warning' : 'success')),
            ])
            ->filters([
                SelectFilter::make('kategori_id')
                    ->relationship('kategori', 'nama_kategori'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
