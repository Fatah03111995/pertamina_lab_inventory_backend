<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Exception;
use Illuminate\Support\Str;

class GasCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Menonaktifkan Log
        DB::disableQueryLog();

        LazyCollection::make(function () {
            $path = database_path('seeders/csv/gas_companies.csv');

            //Apakah File Ada
            if (!file_exists($path)) {
                throw new Exception("File path: $path tidak ada");
            }

            //Buka File
            $file = fopen($path, 'r') ?: throw new Exception("can't open file path: $path");

            //jika masih ada baris baru, lanjutkan perulangan
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
                    $batch [] = [
                        'id' => Str::ulid(),
                        'name' => trim($row[1]),
                        'category' => trim($row[2]),
                        'address' => trim($row[3]),
                        'contact' => trim($row[4])
                    ];
                } catch (\Throwable $e) {
                    // baris corrupt -> lanjut ke baris selanjutnya
                    continue;

                }
            }

            DB::table('gas_companies')->insertOrIgnore($batch);
        });
    }
}
