<?php

namespace App\Filament\Admin\Resources\Vouchers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VouchersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode')->searchable()->sortable()->badge()->color('primary'),
                TextColumn::make('nama')->searchable()->sortable(),
                TextColumn::make('tipe')->badge(),
                TextColumn::make('nilai')->money('idr')->label('Nilai'),
                TextColumn::make('kuota')->label('Kuota'),
                TextColumn::make('terpakai')->label('Terpakai'),
                IconColumn::make('aktif')->boolean(),
                TextColumn::make('berlaku_sampai')->dateTime()->sortable(),
            ])
            ->filters([])
            ->defaultSort('created_at', 'desc')
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
