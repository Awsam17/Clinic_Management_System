<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Spec_doc;
use App\Models\Specialty;
use App\Models\User;
use App\Models\Worked_time;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function getClinics()
    {
        $clinics = Clinic::all();
        return $this->apiResponse($clinics, 'all clinic has been got successfully !', 200);
    }

    public function getSpecialties()
    {
        $specialties = Specialty::all();
        return $this->apiResponse($specialties, 'all specialties has been got successfully !', 200);
    }

    public function getSpecialtyDoctors()
    {
        $specialty_id = $_GET['id'];
        $doctors_data = [];
        $doctors = Doctor::query()
            ->join('spec_docs', 'doctors.id', '=', 'spec_docs.doctor_id')
            ->where('spec_docs.specialty_id', '=', $specialty_id)
            ->get();
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
            $doctors_data[] = $doctor_info;
        }
        if (!empty($doctors_data)) {
            return $this->apiResponse($doctors_data, 'doctors has been got successfully !', 200);
        }
        return $this->apiResponse(null, 'no results !', 404);
    }

    public function getClinicDoctors()
    {
        $clinic_id = $_GET['id'];
        $doctors_data = [];
        $doctors = Doctor::query()
            ->join('doc_clinics', 'doctors.id', '=', 'doc_clinics.doctor_id')
            ->where('doc_clinics.clinic_id', '=', $clinic_id)
            ->get();
        foreach ($doctors as $doctor) {
            $data = $doctor->user;
            $specialties = Specialty::query()
                ->join('spec_docs', 'specialties.id', '=', 'spec_docs.specialty_id')
                ->where('spec_docs.doctor_id', '=', $doctor->id)
                ->select('name AS specialty_name','exp_years AS experience_years')
                ->get();
            $doctor_info = [
                'id' => $doctor->id,
                'name' => $data->name,
                'image' => $data->image,
                'specialties' => $specialties
            ];
            $doctors_data[] = $doctor_info;
        }
        if (!empty($doctors_data)) {
            return $this->apiResponse($doctors_data, 'doctors has been got successfully !', 200);
        }
        return $this->apiResponse(null, 'no results !', 404);
    }

    public function doctorProfile()
    {
        $id = $_GET['id'];
        $doctor = Doctor::find($id);
        if(!empty($doctor)) {
            $data = $doctor->user;
            $specialties = Specialty::query()
                ->join('spec_docs', 'specialties.id', '=', 'spec_docs.specialty_id')
                ->where('spec_docs.doctor_id', '=', $doctor->id)
                ->select('specialty_id','exp_years AS experience_years','name')
                ->get();

            $clinics = Clinic::query()
                ->join('doc_clinics', 'clinics.id', '=', 'doc_clinics.clinic_id')
                ->where('doc_clinics.doctor_id', '=', $doctor->id)
                ->select('clinics.id AS id','name','price','image', DB::raw('total_of_rate / num_of_rate AS rate'))
                ->get();
//            $rate = ($clinics->total_of_rate)/($clinics->num_of_rate);
//            $clinics['rate']=$rate;
            $doctor_info = [
                'id' => $doctor->id,
                'name' => $data->name,
                'phone' => $data->phone,
                'image' => $data->image,
                'gender' => $data->gender,
                'address' => $doctor->address,
                'specialties' => $specialties,
                'clinics' => $clinics
            ];
            return $this->apiResponse($doctor_info, 'doctor'."'".'s profile has been got successfully !', 200);
        }
        return $this->apiResponse(null, "doctor's profile not found !", 200);
    }

    public function doctorProfileInClinic()
    {
        $clinic_id = $_GET['clinic_id'];
        $doctor_id = $_GET['doctor_id'];
        $doctor = Doctor::find($doctor_id);
        if(!empty($doctor)) {
            $data = $doctor->user;
            $specialties = Specialty::query()
                ->join('spec_docs', 'specialties.id', '=', 'spec_docs.specialty_id')
                ->where('spec_docs.doctor_id', '=', $doctor->id)
                ->select('specialty_id','exp_years AS experience_years','name')
                ->get();

            $clinics = Clinic::query()
                ->join('doc_clinics', 'clinics.id', '=', 'doc_clinics.clinic_id')
                ->where('doc_clinics.doctor_id', '=', $doctor->id)
                ->where('clinics.id', '<>', $clinic_id)
                ->select('clinics.id AS id','name','price','image', DB::raw('total_of_rate / num_of_rate AS rate'))
                ->get();

            $worked_times = Worked_time::where([
                ['doctor_id', '=', $doctor_id],
                ['clinic_id', '=', $clinic_id]
            ])->select('day','start','end')->get();

            $doctor_info = [
                'id' => $doctor->id,
                'name' => $data->name,
                'phone' => $data->phone,
                'image' => $data->image,
                'gender' => $data->gender,
                'address' => $doctor->address,
                'specialties' => $specialties,
                'worked_times' => $worked_times,
                'clinics' => $clinics
            ];
            return $this->apiResponse($doctor_info, 'doctor'."'".'s profile has been got successfully !', 200);
        }
        return $this->apiResponse(null, "doctor's profile not found !", 200);
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

    public function searchClinicDoctors()
    {
        $searchText = $_GET['name'];
        $clinic_id = $_GET['id'];

        $users = User::where('name', 'like', '%' . $searchText . '%')
            ->whereHas('doctor', function ($query) use ($clinic_id) {
                $query->whereHas('doctor_clinics', function ($query) use ($clinic_id) {
                    $query->where('clinic_id', $clinic_id);
                });
            })
            ->get();
        $result = [];
        foreach ($users as $user) {
            $doctor = $user->doctor;
            $specialties = Specialty::query()
                ->join('spec_docs', 'specialties.id', '=', 'spec_docs.specialty_id')
                ->where('spec_docs.doctor_id', '=', $doctor->id)
                ->get();
            $temp = [
                'id' => $user->doctor->id,
                'name' => $user->name,
                'gender' => $user->gender,
                'image' => $user->image,
                'phone' => $user->phone,
                'address' => $user->doctor->address,
                'specialty' => $specialties
            ];
            $result[] = $temp;

        }
        if (!empty($result)) {
            return $this->apiResponse($result, 'Ok !', 200);
        }
        return $this->apiResponse(null, 'no results !', 404);

    }

    public function searchSpecialtyDoctors()
    {
        $searchText = $_GET['name'];
        $specialty_id = $_GET['id'];

        $users = User::where('name', 'like', '%' . $searchText . '%')
            ->whereHas('doctor', function ($query) use ($specialty_id) {
                $query->whereHas('specialty_doctors', function ($query) use ($specialty_id) {
                    $query->where('specialty_id', $specialty_id);
                });
            })
            ->get();
        $result = [];
        foreach ($users as $user) {
            $doctor = $user->doctor;
            $specialties = Specialty::query()
                ->join('spec_docs', 'specialties.id', '=', 'spec_docs.specialty_id')
                ->where('spec_docs.doctor_id', '=', $doctor->id)
                ->get();
            $temp = [
                'id' => $user->doctor->id,
                'name' => $user->name,
                'gender' => $user->gender,
                'image' => $user->image,
                'phone' => $user->phone,
                'address' => $user->doctor->address,
                'specialty' => $specialties
            ];
            $result[] = $temp;
        }
        if (! empty($result)) {
            return $this->apiResponse($result, 'Ok !', 200);
        }
        return $this->apiResponse(null, 'no results !', 404);
    }


}
