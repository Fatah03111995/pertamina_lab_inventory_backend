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
                            ->placeholder('Nama Tipe Gas')
                            ->unique('gas_types', 'name', ignoreRecord:true)
                            ->helperText('Nama tidak boleh sama')
                            ->reactive()
                            ->suffix(function ($state) {
                                $length = strlen($state ?? '');
                                return "{$length}/100";
                            })
                            ->maxLength(100)
                            ->required(),

                        TextInput::make('min_stock')
                            ->label('Minimum Stok')
                            ->placeholder('Ex. 0')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Keamanan & Deskripsi')
                    ->schema([
                        TextInput::make('safety_info')
                            ->label('Informasi Keamanan')
                            ->placeholder('Ex. Oxidator')
                            ->reactive()
                            ->suffix(function ($state) {
                                $length = strlen($state ?? '');
                                return "{$length}/100";
                            })
                            ->maxLength(100)
                            ->nullable(),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Ex. Digunakan untuk alat..')
                            ->nullable()
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }
}
