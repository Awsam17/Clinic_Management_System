<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Clinic;
use App\Models\Region;
use App\Models\Specialty;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClinicTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        $faker = Faker::create();
        for ($i=0;$i<20;$i++)
        {
            $data[] = [
                'name' => $faker->name,
                'email' => Str::random(5) . '@gmail.com',
                'phone' => '09' . rand(10000000,99999999),
                'password' =>rand(1000,9999),
                'description' => Str::random(50)
            ];
        }

        foreach($data as $temp)
        {
            $clinic = Clinic::create($temp);
            $address = Address::create([
                'address' => Str::random(15),
                'region_id'=> Region::inRandomOrder()->first()->id,
            ]);
            $address->clinic()->save($clinic);
        }
    }
}
