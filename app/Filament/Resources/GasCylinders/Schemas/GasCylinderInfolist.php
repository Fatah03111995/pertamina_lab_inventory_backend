<?php

namespace App\Filament\Resources\GasCylinders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use App\Enums\GasCylinderStatus;
use Filament\Schemas\Components\Section;

class GasCylinderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Tabung')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama'),
                        TextEntry::make('gasType.name')
                        ->label('Tipe Gas'),
                        TextEntry::make('serial_number'),
                        TextEntry::make('vendor_code')
                            ->label('Kode Vendor')
                            ->default('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Informasi')
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                GasCylinderStatus::EMPTY => 'danger',
                                GasCylinderStatus::FILLED => 'success',
                                GasCylinderStatus::IN_USE => 'info',
                                GasCylinderStatus::MAINTENANCE => 'warning',
                                GasCylinderStatus::LOST => 'gray',
                                GasCylinderStatus::REFILL_PROCESS => 'primary',
                                default => 'secondary',
                            }),
                        TextEntry::make('currentLocation.name')
                            ->label('Lokasi'),
                        TextEntry::make('companyOwner.name')
                            ->label('Pemilik'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
