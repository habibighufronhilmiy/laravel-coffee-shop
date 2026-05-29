<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(150),
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->maxLength(150)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->minLength(8)
                    ->maxLength(255)
                    ->hiddenOn('edit')
                    ->confirmed(),
                TextInput::make('password_confirmation')
                    ->password()
                    ->hiddenOn('edit'),
                TextInput::make('no_telp')
                    ->tel()
                    ->maxLength(20),
                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'kasir' => 'Kasir',
                        'customer' => 'Customer',
                    ])
                    ->hiddenOn('edit')
                    ->required(),
            ]);
    }
}
