<?php

namespace App\Filament\Resources\GasLocations\Schemas;

use App\Enums\GasLocationCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GasLocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                ->schema([
                    TextInput::make('name')
                        ->label('Nama')
                        ->placeholder('Nama Lokasi Gas Cylinder')
                        ->required(),
                    TextInput::make('code')
                        ->unique('gas_locations', 'code', ignoreRecord:true)
                        ->placeholder('Ex. WH-01')
                        ->helperText('Code tidak boleh sama')
                        ->required(),
                    Select::make('category')
                        ->label('Kategori')
                        ->placeholder('Pilih Kategori')
                        ->options(GasLocationCategory::class)
                        ->required(),
                    Textarea::make('address')
                        ->label('Alamat')
                        ->placeholder('Ex. Jalan ...')
                        ->columnSpanFull(),
                ])->columnSpanFull()
            ]);
    }
}
