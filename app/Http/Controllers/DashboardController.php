<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\City;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use ApiResponseTrait;

    public function statistics()
    {
        $user_count = User::count();
        $doctor_count = Doctor::count();
        $clinics_count = Clinic::count();
        $total_price = Appointment::sum('price');
        if ($user_count == 0)
        {
            $male_count = 50;
            $female_count = 50;
        }
        else {
            $male_count = (int)(User::where('gender' , 'male')->count()/$user_count*100);
            $female_count = 100-$male_count;
        }

        $last_doctors = User::where('is_doctor', true)
            ->where('created_at', '>', Carbon::now()->subMonth())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $doctors_data = [] ;

        foreach ($last_doctors as $user) {
            $data = $user->doctor;
            $doctor_info = [
                'id' => $data->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'image' => $user->image,
                'gender' => $user->gender,
                'address' => $data->address
            ];
            $doctors_data[] = $doctor_info;
        }

        $cites = City::get();
        $clinics_per_city = [] ;
        foreach ($cites as $city)
        {
            $city_name = $city->city;
            $clinics = Clinic::whereHas('address.region.city', function ($query) use ($city_name) {
                $query->where('city', $city_name);
            })->count();
            $each_city = [
                $city_name => $clinics,
            ];

            $clinics_per_city[] = $each_city;
        }
        $data = [
            'users_count' => $user_count,
            'doctors_count' => $doctor_count,
            'clinics_count' => $clinics_count,
            'total_price' => $total_price,
            'recent_doctors' => $doctors_data,
            'clinics_per_city' => $clinics_per_city
        ];
        return $this->apiResponse($data,"Data has been got successfully !",200);
    }

    public function getDoctors()
    {
        $users = User::where('is_doctor', true)->get();
        $doctors_data = [] ;

        foreach ($users as $user) {
            $data = $user->doctor;
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
            $doctors_data[] = $doctor_info;
        }

        return $this->apiResponse($doctors_data,"Doctors has been got successfully !",200);
    }

    public function getClinics()
    {
        $clinics = Clinic::join('addresses', 'addresses.id', '=', 'clinics.address_id')
            ->join('regions', 'addresses.region_id', '=', 'regions.id')
            ->join('cities', 'regions.city_id', '=', 'cities.id')
            ->select('clinics.id','clinics.name','clinics.phone','clinics.description','clinics.image','clinics.email','clinics.num_of_doctors AS number_of_doctors',DB::raw('clinics.total_of_rate / clinics.num_of_rate AS rate'), 'addresses.address', 'regions.region', 'cities.city')
            ->get();

        return $this->apiResponse($clinics,"Clinics has been got successfully !",200);
    }

    public function getUsers()
    {
        $users = User::query()->select('id','email','name','phone','image','gender', 'is_doctor' ,'created_at')->get();

        return $this->apiResponse($users ,"Users has been got successfully !",200);
    }

}
