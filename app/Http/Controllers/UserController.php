<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Spec_doc;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\MockObject\Api;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function home()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_data = [
            'name' => $user->name,
            'image' => $user->image,
        ];

        $specialties = Specialty::all();

        $clinics = Clinic::all();

        $data = [
            'user' => $user_data,
            'specialties ' => $specialties,
            'clinics' => $clinics
        ];

        return $this->apiResponse($data, 'home done', 200);
    }

    public function searchClinics()
    {
        $search_text = $_GET['name'];
        $clinics = Clinic::where('name', 'LIKE', '%' . $search_text . '%')->get();
        if (!$clinics->isEmpty()) {
            return $this->apiResponse($clinics, 'successfully searched !', 200);
        }
        return $this->apiResponse(null, 'no results !', 404);
    }

    public function getClinics()
    {
        $clinics = Clinic::all();
        return $this->apiResponse($clinics, 'all clinic has been got successfully !', 200);
    }

    public function getDoctor()
    {
        $all_doctors = [];
        $doctors = Doctor::all();
        // $doctors = Doctor::with('user')->get();
        foreach ($doctors as $doctor) {
            $data = $doctor->user;
            $doctor_info = [
                'id' => $doctor->id,
                'name' => $data->name,
                'phone' => $data->phone,
                'image' => $data->image,
                'gender' => $data->gender,
                'address' => $doctor->address
            ];
            $all_doctors[] = $doctor_info;
        }
        return $this->apiResponse($all_doctors, 'all doctor has been got successfully !', 200);

    }

    public function getSpecialties()
    {
        $specialties = Specialty::all();
        return $this->apiResponse($specialties, 'all specialties has been got successfully !', 200);
    }

    public function searchClinicDoctors($id)
    {
//        $search_text = $_GET['name'];
//        $doctors = Doctor::where('name','LIKE','%'.$search_text.'%')->get();
//        if(!$doctors->isEmpty())
//        {
//
//
//            return $this->apiResponse($doctors , 'successfully searched !' , 200);
//        }
//        return $this->apiResponse(null , 'no results !' , 404);
    }

    public function searchSpecialtyDoctor()
    {
//        $search_text = $_GET['name'];
//        $id = $_GET['id'];
//        $doctors = Spec_doc::where(['specialty_id' => $id ])
//            ->join('doctors','doctors.id','doctor_id')
//            ->get();
//        return $doctors;
//        if (!$doctors->isEmpty()) {
//
//
//
//            return $this->apiResponse($doctors, 'successfully searched !', 200);
//        }
//        return $this->apiResponse(null, 'no results !', 404);
    }

}
