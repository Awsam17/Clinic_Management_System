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
        $end_date = date("Y-m-d");
        $start_date = date("Y")."-01-01";
        $start_timestamp = strtotime($start_date);
        $end_timestamp = strtotime($end_date);

        for ($i=0;$i<30;$i++) {
            $random_timestamp = rand($start_timestamp, $end_timestamp);
            $data[] = [
                'full_name' => $faker->name,
                'date' => $faker->date,
                'user_id' => User::inRandomOrder()->first()->id,
                'doctor_id' => Doctor::inRandomOrder()->first()->id,
                'clinic_id' => Clinic::inRandomOrder()->first()->id,
                'age' => rand(1, 100),
                'gender' => ['male', 'female'][random_int(0, 1)],
                'status' => ['archived', 'pending', 'booked'][random_int(0, 2)],
                'price' => rand(10000, 100000),
                'created_at' => date("Y-m-d", $random_timestamp),
                'updated_at' => date("Y-m-d", $random_timestamp)
            ];
        }

        foreach($data as $da)
        {
            Appointment::create($da);
        }
        }
}
