<?php

namespace App\Filament\Resources\GasCompanies\Schemas;

use App\Enums\GasCompanyCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GasCompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->placeholder('Ex. PT Pemilik Gas Cylinder'),
                        Select::make('category')
                            ->label('Kategori')
                            ->placeholder('Pilih Kategori')
                            ->options(GasCompanyCategory::class)
                            ->required(),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->placeholder('Ex. Jalan ..')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('contact')
                            ->label('Kontak')
                            ->placeholder('08xxxxxxxxxx')
                            ->rule('digits_between:10,13')
                            ->rule('regex:/^(08|\\+62)[0-9]{8,11}$/')
                            ->validationMessages([
                                'digits_between' => 'Nomor HP harus terdiri dari 10 sampai 13 angka.',
                                'regex' => 'Format nomor HP tidak valid. Gunakan format 08xxxx atau +62xxxx.',
                            ])
                            ->required(),
                    ])
                    ->columnSpanFull()
            ]);
    }
}
