<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Clinic;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserTableSeeder::class,
            SpecialtyTableSeeder::class,
            DoctorTableSeeder::class,
            CityTableSeeder::class,
            RegionTableSeeder::class,
            ClinicTableSeeder::class,
            AppointmentTableSeeder::class,
        ]);
    }
}
