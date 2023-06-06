<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            ['region' => 'المزة', 'city_id' => 1],
            ['region' => 'الميدان', 'city_id' => 1],
            ['region' => 'الروضة', 'city_id' => 1],
            ['region' => 'المرج', 'city_id' => 1],
            ['region' => 'القابون', 'city_id' => 1],
            ['region' => 'باب شرقي', 'city_id' => 1],
            ['region' => 'باب توما', 'city_id' => 1],
            ['region' => 'القدم', 'city_id' => 1],
            ['region' => 'الزهراء', 'city_id' => 1],
            ['region' => 'الدوار السابع', 'city_id' => 1],
            ['region' => 'السليمانية', 'city_id' => 1],
            ['region' => 'الفحامة', 'city_id' => 1],
            ['region' => 'الشاغور', 'city_id' => 1],
            ['region' => 'الحميدية', 'city_id' => 1],
            ['region' => 'المزرعة', 'city_id' => 1],
            ['region' => 'المهاجرين', 'city_id' => 1],
            ['region' => 'الركن الدين', 'city_id' => 1],
            ['region' => 'التجارة', 'city_id' => 1],
            ['region' => 'الحي العربي', 'city_id' => 1],
            ['region' => 'الشيخ سعد', 'city_id' => 1],
            ['region' => 'الزبدية', 'city_id' => 1],
            ['region' => 'دمر', 'city_id' => 1],
            ['region' => 'القطنا', 'city_id' => 1],
            ['region' => 'الجرمانا', 'city_id' => 1],
            ['region' => 'جوبر', 'city_id' => 1],
            ['region' => 'التضامن', 'city_id' => 1],
            ['region' => 'السلمانية', 'city_id' => 1],
            ['region' => 'الجديدة', 'city_id' => 1],
            ['region' => 'الشامية', 'city_id' => 1],
            ['region' => 'القنوات', 'city_id' => 1],
            ['region' => 'الشعفة', 'city_id' => 1],
            ['region' => 'بارزة', 'city_id' => 1],
            ['region' => 'الصالحية', 'city_id' => 1],
            ['region' => 'العدوين', 'city_id' => 1],
            ['region' => 'الطريق الدائري', 'city_id' => 1],
            ['region' => 'العباسية', 'city_id' => 1],
            ['region' => 'التل', 'city_id' => 1],
            ['region' => 'الحجر الأسود', 'city_id' => 1],
            ['region' => 'المحمدية', 'city_id' => 1],
            ['region' => 'النهضة', 'city_id' => 1],
            ['region' => 'التحرير', 'city_id' => 1],
            ['region' => 'الشهباء', 'city_id' => 1],
            ['region' => 'المشروع الصناعي', 'city_id' => 1],
            ['region' => 'دوما', 'city_id' => 1],
            ['region' => 'العتمانية', 'city_id' => 1],
            ['region' => 'كفر سوسة', 'city_id' => 1],
            ['region' => 'بين السرايات', 'city_id' => 1],
            ['region' => 'الصالحية الغربية', 'city_id' => 1],
            ['region' => 'الصالحية الشرقية', 'city_id' => 1],
            ['region' => 'الزاهرة', 'city_id' => 1],
            ['region' => 'التجمع الأول', 'city_id' => 1],
            ['region' => 'التجمع الثاني', 'city_id' => 1],
            ['region' => 'التجمع الثالث', 'city_id' => 1],
            ['region' => 'التجمع الرابع', 'city_id' => 1],
            ['region' => 'التجمع الخامس', 'city_id' => 1],
    ];

      foreach ($areas as $area) {
           Region::create($area);
      }
    }
}
