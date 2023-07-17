<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Doc_apply;
use App\Models\Doc_clinic;
use App\Models\Doctor;
use App\Models\Spec_doc;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Comment\Doc;
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
        $is_doctor = Doc_clinic::where(['doctor_id' => $doctor->id , 'clinic_id' => $clinic_id])->get();
        if (!$old_apply->isEmpty() || !$is_doctor->isEmpty())
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

    public function doctor_profile()
    {
        $id = $_GET['id'];

        $data = Doctor::find($id);
        $user = $data->user;

        $specialties = Specialty::query()
            ->join('spec_docs', 'specialties.id', '=', 'spec_docs.specialty_id')
            ->where('spec_docs.doctor_id', '=', $data->id)
            ->select('name AS specialty_name','exp_years AS experience_years')
            ->get();
        $doctor_info = [
            'id' => $data->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'image' => $user->image,
            'gender' => $user->gender,
            'address' => $data->address,
            'specialties' => $specialties
        ];

        return $this->apiResponse($doctor_info , 'doctor profile get successfully' , 200);
    }

    public function doctor_edit(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $doctor = $user->doctor;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'phone' => 'required|string|regex:/^\+?[0-9]{10}$/',
            'image' => 'string',
            'gender' => 'required|string',

        ]);

        $validator_doctor = Validator::make($request->only('address'), [
            'address' => 'string|required',
            'specialties' => 'array',
            'specialties.*.specialty' => 'string|required',
            'specialties.*.exp_years' => 'required|integer'
        ]);

        $user->update(array_merge(
            $validator->validated()
        ));

        $doctor->update(array_merge($validator_doctor->validated()
        ));

        $specialties = $request->specialties ;

        foreach ($specialties as $data)
        {
            $spec = $data['specialty'];
            $exp_years  = $data['exp_years'];
            $specialty = Specialty::where(['name' => $spec ])->first();
            $spec_doc = Spec_doc::create(['exp_years' => $exp_years]);

            $doctor->specialty_doctors()->save($spec_doc);
            $specialty->specialty_doctors()->save($spec_doc);
        }

        return $this->apiResponse(null,'doctor profile updated successfully !',200);
    }

}
