<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Doc_apply;
use App\Models\Doc_clinic;
use App\Models\Doctor;
use App\Models\Worked_time;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClinicController extends Controller
{
    use ApiResponseTrait;

    public function profile()
    {
        $id = $_GET['id'];
        $clinic = Clinic::query()
            ->join('addresses', 'addresses.id', '=', 'clinics.address_id')
            ->join('regions', 'addresses.region_id', '=', 'regions.id')
            ->join('cities', 'regions.city_id', '=', 'cities.id')
            ->select('clinics.id','clinics.name','clinics.phone','clinics.description','clinics.image','clinics.email','clinics.num_of_doctors AS number_of_doctors',DB::raw('clinics.total_of_rate / clinics.num_of_rate AS rate'), 'addresses.address', 'regions.region', 'cities.city')
            ->where('clinics.id',$id)
            ->get();
        return $this->apiResponse($clinic,'ok !',200);
    }




    public function approveDoctor(Request $request)
    {
        $validator = Validator::make($request->all() , [
            'price' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'join_date' => ['required','date'],
            'end_date' => 'date',
            'worked_times' => 'array'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $apply = Doc_apply::find($request->apply_id);
        $doctor = $apply->doctor;
        $clinic = $apply->clinic;
        $doctor_clinic = Doc_clinic::create([
            'price' => $request->price,
            'join_date' => $request->join_date,
            'end_date' => $request->end_date,
        ]);
        $doctor->doctor_clinics()->save($doctor_clinic);
        $clinic->doctor_clinics()->save($doctor_clinic);

        $worked_times = $request->worked_times;
        foreach ($worked_times as $time)
        {
            $times =[];
            for ($i=$time['start'] ; $i< $time['end'] ; $i++)
            {
                array_push($times,strval($i) . ":00");
                array_push($times,strval($i) . ":30");
            }
            //$valuesArray = array_values($times);
           $json_times = json_encode($times);
//            $times2 = json_decode(stripslashes($json_times));
            $worked_time = Worked_time::create([
                'day' => $time['day'],
                'start' => $time['start'],
                'end' => $time['end'],
                'av_times' => $json_times
            ]);
            $doctor->worked_times()->save($worked_time);
            $clinic->worked_times()->save($worked_time);
        }

        $clinic->num_of_doctors++;
        $clinic->save();
        $apply->delete();
        return $this->apiResponse(null,'Done !','200');
    }


}
