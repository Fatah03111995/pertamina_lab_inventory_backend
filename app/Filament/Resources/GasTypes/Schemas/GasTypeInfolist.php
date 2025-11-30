<?php

namespace App\Filament\Resources\GasTypes\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GasTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi Utama')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Gas'),

                        TextEntry::make('min_stock')
                            ->label('Minimum Stok')
                            ->numeric(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Keterangan Tambahan')
                    ->schema([
                        TextEntry::make('safety_info')
                            ->label('Informasi Keamanan')
                            ->placeholder('Tidak ada informasi')
                            ->hidden(fn ($record) => $record->safety_info === null),

                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Tidak ada deskripsi')
                            ->hidden(fn ($record) => empty($record->description)),
                    ])
                    ->columns(1)
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
                    ->collapsed(), // default collapse agar tidak memenuhi layar
            ]);
    }
}
