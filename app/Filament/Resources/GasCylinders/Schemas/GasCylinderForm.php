<?php

namespace App\Filament\Resources\GasCylinders\Schemas;

use App\Enums\GasCylinderStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GasCylinderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Tabung')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->placeholder('Ex. Acetylene-01')
                            ->unique(table: 'gas_cylinders', column: 'name', ignoreRecord: true)
                            ->helperText('Nama tidak boleh sama (Unique)')
                            ->required(),
                        Select::make('gas_type_id')
                            ->label('Tipe Gas Cylinder')
                            ->placeholder('Pilih Salah Satu')
                            ->relationship('gasType', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('serial_number')
                            ->placeholder('YA12xxxxxx')
                            ->required(),
                        TextInput::make('vendor_code')
                            ->label('Kode Vendor')
                            ->placeholder('672123456'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Informasi')
                    ->schema([
                        Select::make('status')
                            ->options(GasCylinderStatus::labels())
                            ->placeholder('Pilih Salah Satu')
                            ->default(GasCylinderStatus::FILLED->value)
                            ->searchable()
                            ->preload()
                            ->visibleOn('create'),
                        Select::make('current_location')
                            ->placeholder('Pilih Salah Satu')
                            ->label('Lokasi')
                            ->relationship('currentLocation', 'name')
                            ->searchable()
                            ->preload()
                            ->visibleOn('create')
                            ->required(),
                        Select::make('company_owner_id')
                            ->label('Pemilik')
                            ->relationship('companyOwner', 'name')
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
