<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class UserTableSeeder extends Seeder
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
                'gender' => ['male', 'female'][random_int(0, 1)],
                'created_at' => now()->toDate(),
                'updated_at' => now()->toDate(),
            ];
        }
        $chunks = array_chunk($data,20);
        foreach($chunks as $chunk)
        {
            User::insert($chunk);
        }
    }
}
