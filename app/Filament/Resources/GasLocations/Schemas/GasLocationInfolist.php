<?php

namespace App\Filament\Resources\GasLocations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GasLocationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                ->schema([
                    TextEntry::make('name')
                        ->label('Nama'),
                    TextEntry::make('code'),
                    TextEntry::make('category')
                        ->label('Kategori'),
                    TextEntry::make('address')
                        ->label('Alamat'),
                ])
                ->columnSpanFull(),
                Section::make('Metadata Sistem')
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Dibuat Pada')
                        ->dateTime('d M Y H:i'),
                    TextEntry::make('updated_at')
                        ->label('Diperbarui Pada')
                        ->dateTime('d M Y H:i'),
                    TextEntry::make('deleted_at')
                        ->label('Dihapus Pada')
                        ->dateTime('d M Y H:i')
                        ->hidden(fn ($record) => $record->deleted_at === null),
                ])
                ->columns(3)
                ->columnSpanFull()
                ->collapsed()
            ]);
    }
}
