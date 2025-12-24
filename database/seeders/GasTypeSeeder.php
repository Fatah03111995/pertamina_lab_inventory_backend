<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

class GasTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::disableQueryLog();

        LazyCollection::make(function () {
            $path = database_path('seeders/csv/gas_types.csv');

            //apakah file ada
            if (!file_exists($path)) {
                throw new Exception("file path: $path is not found");
            }

            $file = fopen($path, 'r');

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
                        'id' => trim($row[0]),
                        'name' => trim($row[1]),
                        'min_stock' => trim($row[2]),
                        'safety_info' => trim($row[3]),
                        'description' => trim($row[4])
                    ];
                } catch (\Throwable $e) {
                    //baris corrupt -> lanjut ke baris selanjutnya
                    continue;
                }

            }
            DB::table('gas_types')->insertOrIgnore($batch);
        });
    }
}
