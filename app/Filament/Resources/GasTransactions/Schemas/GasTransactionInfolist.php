<?php

namespace App\Filament\Resources\GasTransactions\Schemas;

use App\Enums\GasEventType;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GasTransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Informasi Transaksi')
                ->columns(2)
                ->schema([
                    TextEntry::make('document_number')
                        ->label('Nomor Dokumen')
                        ->placeholder('-'),

                    TextEntry::make('event_type')
                        ->label('Tipe Pergerakan')
                        ->formatStateUsing(
                            fn ($state) => $state->label()
                        ),
                ]),

            Section::make('Dokumen Pendukung')
                ->schema([
                    IconEntry::make('evidence_document')
                        ->label('Berita Acara')
                        ->placeholder('Tidak Ada')
                        ->icon(
                            fn ($state) =>
                            $state ? 'heroicon-o-document-text' : 'heroicon-o-minus-circle'
                        )
                        ->color(fn ($state) => $state ? 'primary' : 'gray')
                        ->url(
                            fn ($record) =>
                                $record->evidence_document
                                    ? asset('storage/' . $record->evidence_document)
                                    : null
                        )
                        ->openUrlInNewTab(),
                ]),

            Section::make('Informasi Perusahaan')
                ->schema([
                    TextEntry::make('company.name')
                        ->label('Nama Perusahaan')
                        ->placeholder('-'),
                ]),

            Section::make('Metadata Sistem')
                ->columns(3)
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Dibuat Pada')
                        ->dateTime(),

                    TextEntry::make('updated_at')
                        ->label('Terakhir Diubah')
                        ->dateTime(),

                    TextEntry::make('deleted_at')
                        ->label('Dihapus Pada')
                        ->dateTime(),
                ]),
        ]);
    }
}
