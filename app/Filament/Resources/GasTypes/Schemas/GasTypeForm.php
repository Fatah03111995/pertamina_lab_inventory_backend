<?php

namespace App\Filament\Resources\GasTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class GasTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Gas')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Gas')
                            ->maxLength(100)
                            ->required(),

                        TextInput::make('min_stock')
                            ->label('Minimum Stok')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Keamanan & Deskripsi')
                    ->schema([
                        TextInput::make('safety_info')
                            ->label('Informasi Keamanan')
                            ->maxLength(100)
                            ->nullable(),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
