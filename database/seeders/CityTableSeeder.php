<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Specialty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = ['دمشق' , 'حلب' , 'ريف دمشق' , 'اللاذقية' , 'طرطوس' , 'حمص' , 'حماة' , 'دير الزور' ,'إدلب' , 'السويداء' , 'الحسكة' , 'درعا' , 'القنيطرة' , 'الرقة'];
        for ($i=0;$i<14;$i++)
        {
            $temp['city'] = $cities[$i];
            City::create($temp);
        }
    }
}
