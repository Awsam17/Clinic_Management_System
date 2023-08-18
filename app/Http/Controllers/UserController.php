<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doc_clinic;
use App\Models\Doctor;
use App\Models\Spec_doc;
use App\Models\Specialty;
use App\Models\User;
use App\Models\Worked_time;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\MockObject\Api;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function profile()
    {
        $id = $_GET['id'];
        $user = User::find($id);

        return $this->apiResponse($user , 'user profile get successfully' , 200);
    }

    public function edit(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'phone' => 'required|string|regex:/^\+?[0-9]{10}$/',
            'image' => 'file|mimes:jpg,jpeg',
            'gender' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        if($request->image != null) {
            $file_ex = $request['image']->getClientOriginalExtension();
            $file_name = time() . '.' . $file_ex;
            $file_path = 'images';
            $request->image->move($file_path, $file_name);
        }

        $userData = $validator->validated();
        unset($userData['image']);

        $user->update(array_merge(
            $userData,
        ));
        if($request->image != null)
            $user['image'] = $file_path.'/'.$file_name;
        $user->save();

        return $this->apiResponse(null,'data updated successfully !',200);
    }

    public function home(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_data = [
            'name' => $user->name,
            'image' => $user->image,
        ];

        $lang = $request->header('lang');
        if($lang == 'ar')
            $specialties = Specialty::select('id', 'name' ,'created_at' , 'updated_at')->get();
        else
            $specialties = Specialty::select('id', 'nameEn' ,'created_at' , 'updated_at')->get();

        $clinics = Clinic::query()->join('addresses','addresses.id','=','clinics.address_id')
            ->join('regions','regions.id','=','addresses.region_id')
            ->join('cities','cities.id','=','regions.city_id')
            ->select('clinics.*','cities.city','regions.region','addresses.address')->get();

        $data = [
            'user' => $user_data,
            'specialties ' => $specialties,
            'clinics' => $clinics
        ];

        return $this->apiResponse($data, 'home done', 200);
    }

    public function getClinics()
    {
        $clinics = Clinic::query()->join('addresses','addresses.id','=','clinics.address_id')
        ->join('regions','regions.id','=','addresses.region_id')
        ->join('cities','cities.id','=','regions.city_id')
        ->select('clinics.*','cities.city','regions.region','addresses.address')->get();
        return $this->apiResponse($clinics, 'all clinic has been got successfully !', 200);
    }

    public function getSpecialties(Request $request)
    {
        $lang = $request->header('lang');
        if($lang == 'ar')
            $specialties = Specialty::select('id', 'name' ,'created_at' , 'updated_at')->get();
        else
            $specialties = Specialty::select('id', 'nameEn' ,'created_at' , 'updated_at')->get();
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
                'user_id' => $data->id,
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
            ->select('doctors.*')
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
                'user_id' => $data->id,
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

    public function clinicProfile(){
        $id = $_GET['id'];
        $clinic = Clinic::query()
            ->join('addresses', 'addresses.id', '=', 'clinics.address_id')
            ->join('regions', 'addresses.region_id', '=', 'regions.id')
            ->join('cities', 'regions.city_id', '=', 'cities.id')
            ->select('clinics.id','clinics.name','clinics.phone','clinics.description','clinics.image','clinics.email','clinics.num_of_doctors AS number_of_doctors',DB::raw('clinics.total_of_rate / clinics.num_of_rate AS rate'), 'addresses.address', 'regions.region', 'cities.city')
            ->where('clinics.id',$id)
            ->get();
        return $this->apiResponse($clinic,'ok ',200);
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

    public function availableTimes()
    {
        $clinic_id = $_GET['clinic_id'];
        $doctor_id = $_GET['doctor_id'];
        $available_times = Worked_time::where([
            ['doctor_id', '=', $doctor_id],
            ['clinic_id', '=', $clinic_id]
        ])->select('day','av_times')->get();

        foreach ($available_times as $time) {
            $time->av_times = json_decode($time->av_times);
        }

        return $this->apiResponse($available_times, "Available times has been got successfully !", 200);
    }

    public function makeApp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'string|max:30',
            'age' => 'required|int',
            'description' => 'max:100',
            'gender' => 'string',
            'phone' => 'string',
            'address' => 'string',
            'time' => 'required|string',
            'clinic_id' => 'required|exists:clinics,id' ,
            'doctor_id' => 'required|exists:doctors,id'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $doctorInClinic = Doc_clinic::query()
            -> where (['clinic_id' => $request->clinic_id  , 'doctor_id' => $request->doctor_id])
            -> first();

        if (!$doctorInClinic)
        {
            return $this->apiResponse(null , 'the doctor is not in the clinic!' , 400);
        }


        $request['user_id'] = JWTAuth::parseToken()->authenticate()->id;

        $currentDay = Carbon::now()->dayOfWeek;

        $requestedDay = $request->day;

        $daysDifference = ($requestedDay - $currentDay + 7) % 7;

        if ($requestedDay > 7) {
            return $this->apiResponse(null,'Making appointment failed !',400);
        }

        $dateOfRequestedDay = Carbon::now()->addDays($daysDifference)->format('Y.m.d');
        $request['date'] = $dateOfRequestedDay;

        $prevApp = Appointment::query()->
            where(['clinic_id' => $request->clinic_id , 'doctor_id' => $request->doctor_id , 'date' => $request->date , 'time' => $request->time , 'status'=>'booked'])
        -> first();

        if ($prevApp)
        {
            return $this->apiResponse(null , 'the time is not available' , 400);
        }

        Appointment::create($request->all());
        return $this->apiResponse(null,'Making appointment succeeded !',200);
    }

    public function archivedApps()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $archived_apps = $user->appointments()->where('status', 'archived')
            ->join('clinics', 'appointments.clinic_id' , 'clinics.id')
            ->join('doctors' , 'appointments.doctor_id' , 'doctors.id')
            ->join('users' , 'doctors.user_id' , 'users.id')
            ->select('appointments.id','appointments.user_id','clinics.name as clinic_name' ,'users.name as doctor_name' , 'appointments.full_name' , 'appointments.age' , 'appointments.gender' , 'appointments.date' , 'appointments.time' , 'appointments.description' , 'appointments.hide_user' , 'appointments.status' , 'appointments.price' , 'appointments.created_at', 'appointments.updated_at')
            ->get();
        if ($archived_apps->isEmpty())
        {
            return $this->apiResponse(null,'No archived appointments found !',200);
        }
        return $this->apiResponse($archived_apps,'Done !',200);
    }

    public function bookedApps()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $booked_apps = $user->appointments()->where('status','booked')
            ->join('clinics', 'appointments.clinic_id' , 'clinics.id')
            ->join('doctors' , 'appointments.doctor_id' , 'doctors.id')
            ->join('users' , 'doctors.user_id' , 'users.id')
            ->select('appointments.id','appointments.user_id','clinics.name as clinic_name' ,'users.name as doctor_name' , 'appointments.full_name' , 'appointments.age' , 'appointments.gender' , 'appointments.date' , 'appointments.time' , 'appointments.description' , 'appointments.hide_user' , 'appointments.status' , 'appointments.price' , 'appointments.created_at', 'appointments.updated_at')
            ->get();
        if ($booked_apps->isEmpty())
        {
            return $this->apiResponse(null,'No booked appointments found !',200);
        }
//        $archived_apps = User::query()
//            ->join('appointments','appointments.user_id','=', 'users.id')
//            ->where('users.id','=',)
//            ->where('appointments.status','=','archived')
//            ->select('appointments.*')
//            ->get();
        return $this->apiResponse($booked_apps,'Done !',200);
    }

    public function incomingApps()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $incoming_apps = $user->appointments()->where('status','pending')
            ->join('clinics', 'appointments.clinic_id' , 'clinics.id')
            ->join('doctors' , 'appointments.doctor_id' , 'doctors.id')
            ->join('users' , 'doctors.user_id' , 'users.id')
            ->select('appointments.id','appointments.user_id','clinics.name as clinic_name' ,'users.name as doctor_name' , 'appointments.full_name' , 'appointments.age' , 'appointments.gender' , 'appointments.date' , 'appointments.time' , 'appointments.description' , 'appointments.hide_user' , 'appointments.status' , 'appointments.price' , 'appointments.created_at', 'appointments.updated_at')
            ->get();
        if ($incoming_apps->isEmpty())
        {
            return $this->apiResponse(null,'No incoming appointments found !',200);
        }

//        $archived_apps = User::query()
//            ->join('appointments','appointments.user_id','=', 'users.id')
//            ->where('users.id','=',)
//            ->where('appointments.status','=','archived')
//            ->select('appointments.*')
//            ->get();
        return $this->apiResponse($incoming_apps,'Done !',200);
    }

    public function notifications()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $notifications = $user->notifications;
        if($notifications->isEmpty())
            return $this->apiResponse(null,'No notifications found !',404);
        return $this->apiResponse($notifications,'Notifications has been got successfully !',200);
    }

    public function rate()
    {
        $clinic_id = $_GET['clinic_id'];
        $rate = $_GET['rate'];
        $clinic = Clinic::find($clinic_id);
        if ($clinic)
        {
            $clinic->num_of_rate = $clinic->num_of_rate + 1;
            $clinic->total_of_rate = $clinic->total_of_rate + $rate;
            $clinic->save();
            return $this->apiResponse(null,'Done !',200);
        }
        return $this->apiResponse(null,'Clinic not found !',404);
    }

}
