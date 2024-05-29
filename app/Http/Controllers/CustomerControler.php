<?php

namespace App\Http\Controllers;

use App\Http\Traits\Notifications;
use App\Models\Device;
use App\Models\Notification;
use App\Models\ResetCodePassword;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Traits\Images;

class CustomerControler extends Controller
{
    public function signUp(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', Rule::unique('users')],
            'email' => ['required', 'email', Rule::unique('users')],
            'password' => ['required', 'min:8'],
            'c_password' => ['required', 'same:password'],
            'phone' => ['required', 'string'],
            'photo' => ['required', 'image:jpeg,png,jpg,gif,svg', 'max:2048'],
            'location' => ['required', 'string'],
            'device_token'=>['required',]

        ]);

        if ($validator->fails()) {
            return Response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $image_url = Images::SaveUserImage($request);

        $request['password'] = Hash::make($request['password']);

        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'photo' => $image_url,
            'location' => $request->location,

        ]);

        $user->assignRole(['customer']);

       $device =new Device();
       $device->device_token=$request->device_token;
       $user->deviceable()->save($device);



        $tokenResult = $user->createToken('personal Access Token')->accessToken;
        $data["customer"] = $user;
        $data["tokenType"] = 'Bearer';
        $data["access_token"] = $tokenResult;

        return response()->json($data, Response::HTTP_CREATED);

    }

    /**
     * @throws AuthenticationException
     */

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),
            [
                'name' => ['required_if:email,null', 'string', Rule::exists('users')],
                'email' => ['email', Rule::exists('users')],
                'password' => ['required', 'min:8'],
                'device_token'=>['required',]
            ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if($request->has('email')){
            if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {

                $user = $request->user();
                $tokenResult = $user->createToken('personal Access Token')->accessToken;
                $data['user'] = $user;
                $data["TokenType"] = 'Bearer';
                $data['Token'] = $tokenResult;
            } else {
                throw new AuthenticationException();

            }
        }
        else{
            if (Auth::attempt(['name' => request('name'),'password' => request('password')])) {

                $user = $request->user();
                $tokenResult = $user->createToken('personal Access Token')->accessToken;
                $data['user'] = $user;
                $data["TokenType"] = 'Bearer';
                $data['Token'] = $tokenResult;
            } else {
                throw new AuthenticationException();

            }
        }
        if(!Device::query()->where('deviceable_id','=',$user->id)
            ->where('deviceable_type','=','App\Models\User')->exists())
        {
            $device =new Device();
            $device->device_token=$request->device_token;
            $user->deviceable()->save($device);

        }
        else{
            $exist=false;
            $device=Device::query()->where('deviceable_id','=',$user->id)
                ->where('deviceable_type','=','App\Models\User')->get();
            foreach ($device as $item){
                if($item->device_token == $request->device_token){
                    $exist=true;
                    break;
                }
            }
            if(!$exist){
                $device =new Device();
                $device->device_token=$request->device_token;
                $user->deviceable()->save($device);

            }
        }
            return response()->json($data, Response::HTTP_OK);
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        Auth::user()->token()->revoke();

        return response()->json("logged out", Response::HTTP_OK);

    }

    public function ProfileUpdate(Request $request): \Illuminate\Http\JsonResponse
    {

        $image_url = Images::SaveUserImage($request);

        Auth::guard('api')->user()->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'photo' => $image_url,
            'location' => $request->location,

        ]);

        $user = User::query()->where('id', '=', Auth::guard('api')->id())->get();

        return response()->json($user, Response::HTTP_OK);
    }

    public function MyProfile(): \Illuminate\Http\JsonResponse
    {

        $user = Auth::guard('api')->user();

        return response()->json($user);

    }

    public function ResetPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|exists:password_resets',
            'password' => 'required|string|min:8|',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // find the code
        $passwordReset = ResetCodePassword::query()->where('code', $request->code)->first();

        // check if it is not expired: the time is one hour
        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();

            return response()->json(['message' => 'passwords code_is_expire'], Response::HTTP_UNPROCESSABLE_ENTITY);
            //422 Unprocessable Content

        }

        // find user's email
        $user = User::query()->where('email', $passwordReset->email)->first();

        // update user password
        $request['password'] = Hash::make($request['password']);

        $user->update($request->only('password'));

        // delete current code
        $passwordReset->delete();

        return response()->json(['message' => 'password has been successfully reset'], Response::HTTP_OK);
   }

    public function verifyAccount(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'code' => 'required|string|exists:password_resets',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // find the code
        $code = ResetCodePassword::query()->where('code', $request->code)->first();

        // check if it is not expired: the time is one hour
        if ($code->created_at > now()->addHour()) {
            $code->delete();
            return response()->json(['message' => trans('passwords code is expire')], Response::HTTP_UNPROCESSABLE_ENTITY);
            //422 Unprocessable Content

        }

        // find user's email
        $user = User::query()->where('email', $code->email)->first();

        $date = date("Y-m-d g:i:s");
        $user->email_verified_at = $date;
        $user->save();

        $tokenResult = $user->createToken('personal Access Token')->accessToken;
        $data["message"] = 'Email verified!';
        $data['user'] = $user;
        $data["TokenType"] = 'Bearer';
        $data['Token'] = $tokenResult;
        return response()->json($data, Response::HTTP_OK);

    }
    public function refreshToken(Request $request){
        $validator=Validator::make($request->all(),[
            'id'=>['required',Rule::exists('users')],
            'token'=>['required','string']
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors());
        }

        Notifications::refreshToken($request->id,$request->token,'user');

        return response()->json('token refreshed' ,Response::HTTP_OK);
    }

   public function getNotifications(){
        $user=Auth::guard('api')->id();
        $notifications=Notification::query()
            ->join('notifiables','notifications.id','=','notifiables.notification_id')
            ->where('notifiable_id','=',$user)
            ->where('notifiable_type','=','App/Models/User')
            ->get();
        return response()->json($notifications,Response::HTTP_OK);
   }

}
