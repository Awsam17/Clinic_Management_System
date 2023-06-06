<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Doc_apply;
use Illuminate\Http\Request;
use PHPUnit\Framework\Constraint\IsEmpty;
use Tymon\JWTAuth\Facades\JWTAuth;

class DoctorController extends Controller
{
    use ApiResponseTrait;

    public function apply(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $doctor= $user->doctor;
        $clinic_id = $_GET['id'];
        $old_apply = Doc_apply::where(['doctor_id' => $doctor->id, 'clinic_id' => $clinic_id])->get();
        if (!$old_apply->isEmpty())
        {
            return $this->apiResponse(null,'you already applied','200');
        }
        $description = $request->description;

        $clinic = Clinic::find($clinic_id);
        $apply = Doc_apply::create([
            'description' => $description
        ]);
        $doctor->doctor_applies()->save($apply);
        $clinic->doctor_applies()->save($apply);

        return $this->apiResponse(null,'Done !','200');
    }
}
