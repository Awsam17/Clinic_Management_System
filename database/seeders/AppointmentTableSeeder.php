<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AppointmentTableSeeder extends Seeder
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
                'full_name' => $faker->name,
                'date' => $faker->date,
                'user_id'=>  User::inRandomOrder()->first()->id,
                'doctor_id'=>  Doctor::inRandomOrder()->first()->id,
                'clinic_id'=>  Clinic::inRandomOrder()->first()->id,
                'age' => rand(1,100),
                'gender' => ['male', 'female'][random_int(0, 1)],
                'status' => ['archived', 'pending', 'booked'][random_int(0, 2)],
                'price' => rand(10000,100000),
            ];

            foreach($data as $da)
            {
                Appointment::create($da);
            }
        }
    }
}
