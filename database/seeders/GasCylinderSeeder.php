<?php

namespace Database\Seeders;

use App\Models\GasCompany;
use App\Models\GasLocation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Exception;
use Illuminate\Support\Str;

class GasCylinderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::disableQueryLog();

        LazyCollection::make(function () {
            $path = database_path('seeders/csv/gas_cylinders.csv');

            //apakah file ada
            if (!file_exists($path)) {
                throw new Exception("file path: $path is not found");
            }

            $file = fopen($path, 'r') ?: throw new Exception("Can't open file path:$path");

            //Perulangan jika baris masih ada
            while (($row = fgetcsv($file, null, ';')) !== false) {
                yield $row;
            }

            fclose($file);
        })
        ->skip(1)
        ->chunk(500)
        ->each(function ($chunk) {
            $batch = [];

            foreach ($chunk as $row) {
                try {
                    $batch[] = [
                        'id' => Str::ulid(),
                        'name' => trim($row[1]),
                        'gas_type_id' => trim($row[2]),
                        'serial_number' => trim($row[3]),
                        'vendor_code' => trim($row[4]),
                        'status' => trim($row[5]),
                        'current_location_id' => GasLocation::first()->id,
                        'company_owner_id' => GasCompany::first()->id,
                        'metadata' => null
                    ];
                } catch (\Throwable $e) {
                    //baris corrupt -> lanjut ke baris selanjutnya
                    continue;
                }

            }
            DB::table('gas_cylinders')->insertOrIgnore($batch);
        });
    }
}
