<?php

namespace App\Filament\Resources\GasEvents\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GasEventInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('gasCylinder.name'),
                TextEntry::make('gasTransaction.id'),
                TextEntry::make('event_type'),
                TextEntry::make('fromLocation.name'),
                TextEntry::make('toLocation.name'),
                TextEntry::make('from_status'),
                TextEntry::make('to_status'),
                TextEntry::make('created_by'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                TextEntry::make('deleted_at')
                    ->dateTime(),
            ]);
    }
}
