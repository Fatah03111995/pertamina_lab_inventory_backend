<?php

namespace App\Filament\Resources\GasTransactions\Schemas;

use App\Enums\GasEventType;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\GasLocation;
use App\Enums\GasCylinderStatus;
use App\Models\GasCylinder;

class GasTransactionForm
{
    protected static function needDocumentEvidence(?string $eventType): bool
    {
        if (!$eventType) {
            return false;
        }
        return in_array($eventType, [
            GasEventType::TAKE_FOR_REFILL->value,
            GasEventType::RETURN_FROM_REFILL->value,
            GasEventType::MOVEMENT_EXTERNAL->value ]);
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([

            Section::make('Informasi Transaksi')
                ->schema([
                    Select::make('event_type')
                        ->label('Tipe Pergerakan')
                        ->placeholder('Pilih Tipe Pergerakan')
                        ->options(GasEventType::labels())
                        ->reactive()
                        ->required(),

                    Select::make('to_location_id')
                        ->label('Lokasi Tujuan')
                        ->placeholder('Pilih Tujuan')
                        ->options(GasLocation::all()->pluck('name', 'id'))
                        ->hidden(fn ($get) => in_array($get('event_type'), [
                            GasEventType::MARK_EMPTY->value,
                        ])),
                ])
                ->columnSpanFull(),

                Section::make('Evidence')
                ->description('Pergerakan untuk pengisian dan pergerakan ke pihak External harus melampirkan dokumen BA atau sejenisnya')
                ->schema([
                    TextInput::make('document_number')
                        ->label('Document Number')
                        ->placeholder('Ex. BA/PEPC/X/2025 ..')
                        ->maxLength(100)
                        ->required(function ($get) {
                            return static::needDocumentEvidence($get('event_type'));
                        }),
                    FileUpload::make('evidence_document')
                        ->label('Evidence Document')
                        ->helperText('PDF, max 5MB')
                        ->directory('gas/evidence')
                        ->preserveFilenames()
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(5120)
                        ->required(function ($get) {
                            return static::needDocumentEvidence($get('event_type'));
                        }),
                        ])
                        ->columnSpanFull(),

                Section::make()
                    ->schema([
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Tulis Catatan..')
                            ->helperText('Lapor hilang/ressolve issue harus mencantumkan catatan')
                            ->required(function ($get) {
                                return in_array(
                                    $get('event_type'),
                                    [
                                        GasEventType::REPORT_LOST,
                                        GasEventType::RESOLVE_ISSUE
                                    ]
                                );
                            })
                ])
                ->columnSpanFull(),

                Section::make('Rincian Tabung')
                ->schema([
                    Select::make('gas_cylinder_id')
                                ->label('Tabung Gas')
                                ->options(
                                    GasCylinder::query()
                                    ->pluck('name', 'id')
                                )
                                ->searchable()
                                ->preload()
                                ->multiple()
                                ->required(),

                    Select::make('to_status')
                                ->label('Status Akhir')
                                ->options(GasCylinderStatus::labels())
                                ->visible(fn ($get) => in_array($get('event_type'), [
                                    'return_from_refill', 'maintenance_end', 'movement_external', 'movement_internal',
                                ])),
                ])->columnSpanFull()
        ]);
    }
}
