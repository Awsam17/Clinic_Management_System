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
        $specialities2 = ['Cardiology', 'Gastroenterology', 'Dermatology', 'Neurology', 'Ophthalmology', 'Otolaryngology (ENT)', 'Endocrinology', 'Gynecology', 'Pulmonology', 'Pediatrics', 'Rheumatology', 'Oncology', 'Hematology', 'Nephrology', 'Internal Medicine', 'Orthopedic Surgery', 'Neurosurgery', 'Plastic Surgery', 'Urology', 'Cardiac Surgery', 'Thoracic Surgery', 'General Surgery', 'Pediatric Surgery', 'Vascular Surgery', 'Dentistry', 'Psychiatry', 'Physical Medicine', 'Laboratory Medicine', 'Radiology', 'Pathology'];
        for ($i=0;$i<30;$i++)
        {
            $temp['name'] = $specialities[$i];
            $temp['nameEn'] = $specialities2[$i];
            Specialty::insert($temp);
        }
    }
}
