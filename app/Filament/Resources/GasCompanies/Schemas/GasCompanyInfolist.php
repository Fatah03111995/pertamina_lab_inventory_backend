<?php

namespace App\Filament\Resources\GasCompanies\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class GasCompanyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama'),
                        TextEntry::make('category')
                            ->label('Kategori')
                            ->badge(),
                        TextEntry::make('contact')
                            ->label('Kontak'),
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
