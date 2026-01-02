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
use App\Enums\GasLocationCategory;
use App\Models\GasCylinder;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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

    protected static function getLocationOptions(?string $eventType)
    {
        if (!$eventType) {
            return collect();
        }
        return match ($eventType) {
            GasEventType::MOVEMENT_EXTERNAL->value =>
                GasLocation::where('category', GasLocationCategory::VENDOR)
                    ->pluck('name', 'id'),

            GasEventType::MOVEMENT_INTERNAL->value =>
                GasLocation::where('category', '!=', GasLocationCategory::VENDOR)
                    ->pluck('name', 'id'),

            GasEventType::MAINTENANCE_START->value =>
                GasLocation::where('category', GasLocationCategory::MAINTENANCE)
                    ->pluck('name', 'id'),

            default =>
                GasLocation::pluck('name', 'id'),
        };
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
                    ->placeholder(function ($get) {
                        if (! $get('event_type')) {
                            return 'Pilih Lokasi Tujuan';
                        }

                        if (static::getLocationOptions($get('event_type'))->isEmpty()) {
                            return 'Lokasi Belum Tersedia';
                        }

                        return 'Pilih Lokasi Tujuan';
                    })
                    ->reactive()
                    ->options(function ($get) {
                        return static::getLocationOptions($get('event_type'));
                    })
                    ->hidden(
                        fn ($get) =>
                        $get('event_type') === GasEventType::MARK_EMPTY->value
                    )
                    ])->columnSpanFull(),


                Section::make('Evidence')
                ->description('Pergerakan untuk pengisian dan pergerakan ke pihak External harus melampirkan dokumen BA atau sejenisnya')
                ->schema([
                    TextInput::make('document_number')
                        ->label('Document Number')
                        ->placeholder('Ex. BA/PEPC/X/2025 ..')
                        ->unique()
                        ->maxLength(100)
                        ->required(function ($get) {
                            return static::needDocumentEvidence($get('event_type'));
                        }),
                    FileUpload::make('evidence_document')
                        ->label('Evidence Document')
                        ->helperText('PDF, max 5MB')
                        ->disk('public')
                        ->directory('gas/evidence')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(5120)
                        ->required(function ($get) {
                            return static::needDocumentEvidence($get('event_type'));
                        })
                        ->storeFiles(false)
                        ->rules([
                            function ($attribute, $value, $fail) {
                                if (! $value) {
                                    return;
                                }

                                if (! $value->exists()) {
                                    $fail('File tidak ditemukan. Silakan upload ulang.');
                                    return;
                                }

                                if (! $value instanceof TemporaryUploadedFile) {
                                    $fail('File tidak valid atau sudah tidak tersedia.');
                                    return;
                                }

                                if ($value->getSize() > 5 * 1024 * 1024) {
                                    $fail('Ukuran file maksimal 5 MB.');
                                }
                            },
                        ])
                        ->getUploadedFileNameForStorageUsing(function ($file, $get) {
                            $documentNumber = $get('document_number');

                            if (blank($documentNumber)) {
                                return Str::ulid(). '.pdf';
                            }

                            $safeName = Str::slug($documentNumber, '-');
                            return $safeName . '.' . $file->getClientOriginalExtension();
                        })
                        ->preserveFilenames(),
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
                                ->hiddenOn('edit')
                                ->visible(fn ($get) => in_array($get('event_type'), [
                                    'maintenance_start','maintenance_end', 'movement_external', 'movement_internal',
                                ])),
                ])->columnSpanFull()
                            ]);
    }
}
