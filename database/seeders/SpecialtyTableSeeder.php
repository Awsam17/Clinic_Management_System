<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecialtyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialities = ['قلبية' , 'هضمية' , 'جلدية' , 'عصبية'  , 'عينية' , 'أذنية' ,  'غدية' ,  'نسائية'  , 'صدرية' , 'أطفال' , 'رثوية ومفاصل' ,  'أورام' ,  'أمراض الدم' , 'أمراض الكلية' ,  'داخلية عامة' ,  'جراحة عظمية' ,  'جراحة عصبية' ,  'جراحة تجميلية' ,  'جراحة بولية' ,  'جراحة قلبية' ,  'جراحة صدرية' ,  'جراحة عامة' ,  'جراحة أطفال' ,  'جراحة أوعية' ,  'طب أسنان' ,  'نفسية',   'طب فيزيائي' ,  'مخبري' , ' أشعة' ,  'تشريح مرضي'];
        for ($i=0;$i<30;$i++)
        {
            $temp['name'] = $specialities[$i];
            Specialty::insert($temp);
        }
    }
}
