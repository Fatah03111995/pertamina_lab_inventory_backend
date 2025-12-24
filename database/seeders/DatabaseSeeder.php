<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'abdul fatah',
            'email' => 'fatah03111995@gmail.com',
            'password' => Hash::make('123456')
        ]);
        $this->call([
            GasTypeSeeder::class,
            GasLocationSeeder::class,
            GasCompanySeeder::class,
            GasCylinderSeeder::class,
        ]);
    }
}
