<?php

namespace App\Filament\Resources\GasEvents\Schemas;

use App\Enums\GasCylinderStatus;
use App\Enums\GasEventType;
use App\Enums\GasLocationCategory;
use App\Models\GasLocation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class GasEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('event_type')
                ->label('Jenis Event')
                ->options(GasEventType::labels())
                ->required()
                ->live(),

            Select::make('gas_cylinder_id')
                ->label('Tabung Gas')
                ->relationship('gasCylinder', 'name')
                ->multiple()
                ->required(),

            Select::make('gas_transaction_id')
                ->label('Transaksi')
                ->relationship('gasTransaction', 'id')
                ->visible(fn ($get) => in_array($get('event_type'), [
                    'take_for_refill', 'return_from_refill', 'movement_external',
                ])),

            Select::make('from_location_id')
                ->label('Lokasi Asal')
                ->options(GasLocation::all()->pluck('name', 'id'))
                ->visible(fn ($get) => in_array($get('event_type'), [
                    'take_for_refill', 'movement_external', 'movement_internal', 'maintenance_end',
                ])),

            Select::make('to_location_id')
                ->label('Lokasi Tujuan')
                ->options(GasLocation::all()->pluck('name', 'id'))
                ->visible(fn ($get) => in_array($get('event_type'), [
                    'take_for_refill', 'return_from_refill', 'movement_external', 'movement_internal', 'maintenance_start', 'maintenance_end',
                ])),

            Select::make('from_status')
                ->label('Status Awal')
                ->options(GasCylinderStatus::labels())
                ->visible(fn ($get) => in_array($get('event_type'), [
                    'maintenance_end', 'movement_external', 'movement_internal',
                ])),

            Select::make('to_status')
                ->label('Status Akhir')
                ->options(GasCylinderStatus::labels())
                ->visible(fn ($get) => in_array($get('event_type'), [
                    'return_from_refill', 'maintenance_end', 'movement_external', 'movement_internal',
                ])),

            Textarea::make('notes')
                ->label('Catatan')
                ->columnSpanFull(),

            // Optional: created_by, metadata (hidden from user, or advanced only)
        ]);
    }
}
