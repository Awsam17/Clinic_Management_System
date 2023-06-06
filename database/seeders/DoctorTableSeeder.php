<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Spec_doc;
use App\Models\Specialty;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DoctorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        $faker = Faker::create();
        for ($i=0;$i<5;$i++)
        {
            $data[] = [
                'name' => $faker->name,
                'email' => Str::random(5) . '@gmail.com',
                'phone' => '09' . rand(10000000,99999999),
                'password' =>rand(1000,9999),
                'gender' => ['male', 'female'][random_int(0, 1)],
                'is_doctor' => 1,
            ];
        }

        foreach($data as $t)
        {
            $user = User::create($t);
            $data1 = [
                'address' => Str::random(10),
                ];
            $doctor = Doctor::create($data1);
            $user->doctor()->save($doctor);
            $specialty = Specialty::inRandomOrder()->first();
            $spec_doc = Spec_doc::create([
                'exp_years' => rand(1,9)
            ]);
            $doctor->specialty_doctors()->save($spec_doc);
            $specialty->specialty_doctors()->save($spec_doc);
        }
    }
}
