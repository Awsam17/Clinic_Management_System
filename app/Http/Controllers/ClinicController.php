<?php

namespace App\Http\Controllers;

use App\Models\Doc_apply;
use App\Models\Doc_clinic;
use App\Models\Doctor;
use App\Models\Worked_time;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClinicController extends Controller
{
    use ApiResponseTrait;

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
        $apply->delete();
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
                array_push($times,$i . ':00');
                array_push($times,$i . ':30');
            }
            $json_times = json_encode($times);
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
        return $this->apiResponse(null,'Done !','200');
    }
}
