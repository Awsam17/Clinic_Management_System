<?php

namespace App\Http\Controllers;

use App\Mail\SendCodeResetPassword;
use App\Mail\SendCodeVerification;
use App\Models\Address;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Region;
use App\Models\ResetCodePassword;
use App\Models\Secretary;
use App\Models\Spec_doc;
use App\Models\Specialty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    use ApiResponseTrait;

    public function __construct() {
        $this->middleware('auth:user')->only(['userLogout']);
        $this->middleware('auth:clinic')->only(['clinicLogout','secretaryRegister']);
        $this->middleware('auth:secretary')->only(['secretaryLogout']);
    }

    public function userLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:4',
            'device_key' => 'string|required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth('user')->attempt(['email' => $request->email,'password' => $request->password])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = User::query()
            ->where('email',$request->email)
            ->first();
        $user->device_key = $request->device_key;
        $user->save();

        if ($user->email_verified_at == null)
            return $this->apiResponse(null,'You have to verify first !',401);
        return $this->createNewToken($token , 'user');
    }

    public function userRegister(Request $request) {
        $validator = Validator::make($request->all() , [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:4',
            'phone' => 'required|string|regex:/^\+?[0-9]{10}$/',
            'image' => 'file|mimes:jpg,jpeg',
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
        if($request->image != null) {
            $file_ex = $request['image']->getClientOriginalExtension();
            $file_name = time() . '.' . $file_ex;
            $file_path = 'images';
            $request->image->move($file_path, $file_name);
        }
        $userData = $validator->validated();
        unset($userData['image']);

        $user = User::create(array_merge(
            $userData,
            ['password' => bcrypt($request->password)]
        ));
        if($request->image != null)
            $user['image'] = $file_path.'/'.$file_name;
        $user->save();
//        $token = auth('user')->attempt($validator->validated());
//        return $this->createNewToken($token,'user');
        return $this->apiResponse(null,'created successfully waiting for verify !',200);
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
        if($request->image != null) {
            $file_ex = $request['image']->getClientOriginalExtension();
            $file_name = time() . '.' . $file_ex;
            $file_path = 'images';
            $request->image->move($file_path, $file_name);
        }

        $user = User::create($request->except('address'));
        if($request->image != null)
            $user['image'] = $file_path.'/'.$file_name;
        $user->save();
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

        return $this->apiResponse(null,'created successfully waiting for verify !',200);
    }

    public function userLogout() {
        auth('user')->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    public function userForgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users'
        ]);

        ResetCodePassword::query()->where('email',$request->email)->delete();

        $data['code'] = mt_rand(100000,999999);

        ResetCodePassword::query()->create($data);

        Mail::to($request->email)->send(new SendCodeResetPassword($data['code']));

        return $this->apiResponse(null,'email sent successfully !',200);
    }

    public function userCheckCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords'
        ]);

        $reset = ResetCodePassword::query()->where('code',$request->code)->first();

        if ($reset->created_at > now()->addDay())
        {
            $reset->delete();
            return response()->json([
                'message' => 'code is expired !'
            ]);
        }
        $data['code'] = $reset->code;
        return $this->apiResponse($data,'code is valid !',200);
    }

    public function userResetPassword(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords',
            'password' => 'required|confirmed'
        ]);

        $reset = ResetCodePassword::query()->where('code',$request->code)->first();

        if ($reset->created_at > now()->addDay())
        {
            $reset->delete();
            return response()->json([
                'message' => 'code is expired !'
            ]);
        }

        $user = User::query()->where('email',$reset->email);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $reset->delete();

        return $this->apiResponse(null,'password successfully reset !',200);
    }

    public function userRequestVerify(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $code = mt_rand(100000,999999);
        $user = User::query()->where('email',$request->email)->update(['code'=>$code]);
        Mail::to($request->email)->send(new SendCodeVerification($code));

        return $this->apiResponse(null,'email sent successfully !',200);
    }

    public function userVerify(Request $request)
    {
        $request->validate([
            'code' => 'required|exists:users|string',
        ]);
        $user = User::where('code',$request->code)->first();
        $user->email_verified_at = Carbon::now();
        $user->save();
        $token = auth('user')->login($user);
        $user['token'] = $token;
//        $data = [
//            'user' => $user,
//            'token' => $token
//        ];
        return $this->apiResponse($user,'user successfully verified !',200);
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
            'image' => 'file|mimes:jpg,jpeg',
            'description' => 'string|max:500',
        ]);
        if ($validator->fails()) {
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

        $clinic = Clinic::create(array_merge(
            $userData,
            ['password' => bcrypt($request->password)]
        ));

        if($request->image != null)
            $clinic['image'] = $file_path.'/'.$file_name;
        $clinic->save();

        $address = Address::create([
            'address' => $request->address,
            'region_id' => $request->region_id
        ]);
        $clinic->address_id = $address->id;
        $clinic->save();
        return $this->apiResponse(null,'created successfully waiting for verify !',200);
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
        $clinic = Clinic::where('email',$request->email)->first();
        if ($clinic->email_verified_at == null)
            return $this->apiResponse(null,'You have to verify first !',401);
        return $this->createNewToken($token,'clinic');
    }

    public function clinicLogout() {
        auth('clinic')->logout();
        return response()->json(['message' => 'Clinic successfully signed out']);
    }

    public function clinicForgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:clinics'
        ]);

        ResetCodePassword::query()->where('email',$request->email)->delete();

        $data['code'] = mt_rand(100000,999999);

        ResetCodePassword::query()->create($data);

        Mail::to($request->email)->send(new SendCodeResetPassword($data['code']));

        return $this->apiResponse(null,'email sent successfully !',200);
    }

    public function clinicCheckCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords'
        ]);

        $reset = ResetCodePassword::query()->where('code',$request->code)->first();

        if ($reset->created_at > now()->addDay())
        {
            $reset->delete();
            return response()->json([
                'message' => 'code is expired !'
            ]);
        }

        $data['code'] = $reset->code;
        return $this->apiResponse($data,'code is valid !',200);
    }

    public function clinicResetPassword(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords',
            'password' => 'required|confirmed'
        ]);

        $reset = ResetCodePassword::query()->where('code',$request->code)->first();

        if ($reset->created_at > now()->addDay())
        {
            $reset->delete();
            return response()->json([
                'message' => 'code is expired !'
            ]);
        }

        $clinic = Clinic::query()->where('email',$reset->email);

        $clinic->update([
            'password' => Hash::make($request->password),
        ]);

        $reset->delete();

        return $this->apiResponse(null,'password successfully reset !',200);
    }

    public function clinicRequestVerify(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $code = mt_rand(100000,999999);
        $clinic = Clinic::query()->where('email',$request->email)->update(['code'=>$code]);
        Mail::to($request->email)->send(new SendCodeVerification($code));

        return $this->apiResponse(null,'email sent successfully !',200);
    }

    public function clinicVerify(Request $request)
    {
        $request->validate([
            'code' => 'required|exists:clinics|string',
        ]);
        $clinic = Clinic::where('code',$request->code)->first();
        $clinic->email_verified_at = Carbon::now();
        $clinic->save();
        $token = auth('clinic')->login($clinic);

        $clinic['token'] = $token;
//        $data = [
//            'user' => $clinic,
//            'token' => $token
//        ];
        return $this->apiResponse($clinic,'clinic successfully verified !',200);
    }

    /// Scretary auth !!!!!


    public function secretaryRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:secretaries',
            'password' => 'required|string|min:4',
            'clinic_id' => 'required|exists:clinics,id'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        Secretary::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        return $this->apiResponse(null , 'created successfully !' , 200 );
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

    public function secretaryForgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:secretaries'
        ]);

        ResetCodePassword::query()->where('email',$request->email)->delete();

        $data['code'] = mt_rand(100000,999999);

        ResetCodePassword::query()->create($data);

        Mail::to($request->email)->send(new SendCodeResetPassword($data['code']));

        return $this->apiResponse(null,'email sent successfully !',200);
    }

    public function secretaryCheckCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords'
        ]);

        $reset = ResetCodePassword::query()->where('code',$request->code)->first();

        if ($reset->created_at > now()->addDay())
        {
            $reset->delete();
            return response()->json([
                'message' => 'code is expired !'
            ]);
        }

        $data['code'] = $reset->code;
        return $this->apiResponse($data,'code is valid !',200);
    }

    public function secretaryResetPassword(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords',
            'password' => 'required|confirmed'
        ]);

        $reset = ResetCodePassword::query()->where('code',$request->code)->first();

        if ($reset->created_at > now()->addDay())
        {
            $reset->delete();
            return response()->json([
                'message' => 'code is expired !'
            ]);
        }

        $secretary = Secretary::query()->where('email',$reset->email);

        $secretary->update([
            'password' => Hash::make($request->password),
        ]);

        $reset->delete();

        return $this->apiResponse(null,'password successfully reset !',200);
    }

}
