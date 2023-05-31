<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Secretary;
use App\Models\Spec_doc;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    use ApiResponseTrait;

    public function __construct() {
        $this->middleware('auth:user')->only(['userLogout']);
        $this->middleware('auth:clinic')->only(['clinicLogout']);
        $this->middleware('auth:secretary')->only(['secretaryLogout']);
    }

    public function userLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:4',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth('user')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token , 'user');
    }

    public function userRegister(Request $request) {
        $validator = Validator::make($request->all() , [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:4',
            'phone' => 'required|string|regex:/^\+?[0-9]{10}$/',
            'image' => 'string',
            'gender' => 'string|required',
            'is_doctor' => 'boolean',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        if ($request->is_doctor)
        {
            return $this->apiResponse(null,'data validated successfully !',202);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        $token = auth('user')->attempt($validator->validated());
        return $this->createNewToken($token,'user');
    }

    public function continueAsdoctor(Request $request)
    {
        $validator = Validator::make($request->only('address'), [
            'address' => 'string|required',
            'specialties' => 'array',
            'specialties.*.specialty' => 'string|required',
            'specialties.*.exp_years' => 'required|integer'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create($request->except('address'));
        $doctor = Doctor::create($request->only('address'));
        $user->doctor()->save($doctor);
        // specilaities ..
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

        $token = auth('user')->attempt($request->only('email','password'));
        return $this->createNewToken($token,'user');
    }

    public function userLogout() {
        auth('user')->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    public function refresh() {
       // return $this->createNewToken(auth('user')->refresh());
    }

    public function userProfile() {
        return response()->json(auth('user')->user());
    }

    protected function createNewToken($token,$guard){
        $user = auth($guard)->user();
        $user['token'] = $token;
        return $this->apiResponse($user,'success !',202);
    }


    /// clinic auth !!!!!


    public function clinicRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:clinics',
            'password' => 'required|string|min:4',
            'phone' => 'required|string|regex:/^\+?[0-9]{10}$/',
            'image' => 'string',
            'description' => 'string|max:500'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $clinic = Clinic::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        $token = auth('clinic')->attempt($validator->validated());
        return $this->createNewToken($token,'clinic');
    }

    public function clinicLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:4',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth('clinic')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token,'clinic');
    }

    public function clinicLogout() {
        auth('clinic')->logout();
        return response()->json(['message' => 'Clinic successfully signed out']);
    }


    /// Scretary auth !!!!!


    public function secretaryRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:secretaries',
            'password' => 'required|string|min:4',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $secretary = Secretary::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        $token = auth('secretary')->attempt($validator->validated());
        return $this->createNewToken($token,'secretary');
    }

    public function secretaryLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:4',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth('secretary')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token,'secretary');
    }

    public function secretaryLogout() {
        auth('secretary')->logout();
        return response()->json(['message' => 'Secretary successfully signed out']);
    }
}
