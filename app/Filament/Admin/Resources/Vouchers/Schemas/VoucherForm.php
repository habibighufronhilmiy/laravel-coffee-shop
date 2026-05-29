<?php

namespace App\Filament\Admin\Resources\Vouchers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->helperText('Kode unik yang akan dimasukkan customer saat checkout.'),
                TextInput::make('nama')
                    ->required()
                    ->maxLength(150)
                    ->label('Nama Voucher'),
                Select::make('tipe')
                    ->options([
                        'persen' => 'Persen (%)',
                        'nominal' => 'Nominal (Rp)',
                    ])
                    ->required(),
                TextInput::make('nilai')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->label('Nilai Diskon')
                    ->helperText('Contoh: 10 = 10% (jika tipe Persen) atau 10000 = Rp 10.000 (jika tipe Nominal)'),
                TextInput::make('min_belanja')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->label('Minimal Belanja (Rp)'),
                TextInput::make('maks_diskon')
                    ->numeric()
                    ->nullable()
                    ->minValue(0)
                    ->label('Maksimal Diskon (Rp)')
                    ->helperText('Khusus tipe Persen. Kosongkan jika tidak ada batas.'),
                TextInput::make('kuota')
                    ->numeric()
                    ->nullable()
                    ->minValue(1)
                    ->label('Kuota Pemakaian')
                    ->helperText('Kosongkan jika tidak terbatas.'),
                DateTimePicker::make('berlaku_mulai')
                    ->label('Berlaku Mulai'),
                DateTimePicker::make('berlaku_sampai')
                    ->label('Berlaku Sampai')
                    ->after('berlaku_mulai')
                    ->rule('after:berlaku_mulai'),
                Toggle::make('aktif')
                    ->default(true),
            ]);
    }
}
