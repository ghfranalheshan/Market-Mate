<?php

namespace App\Http\Controllers;

use App\Http\Traits\Images;
use App\Http\Traits\Notifications;
use App\Models\Delivery;
use App\Models\Device;
use App\Models\FollowUpRequest;
use App\Models\M_category;
use App\Models\Market;
use App\Http\Requests\StoreMarketRequest;
use App\Http\Requests\UpdateMarketRequest;
use App\Models\Notification;
use App\Models\Position;
use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class MarketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\jsonResponse
     */
    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', Rule::unique('markets')],
            'email' => ['required', 'email', Rule::unique('markets')],
            'password' => ['required', 'min:8'],
            'c_password' => ['required', 'same:password'],
            'phone' => ['required', 'string'],
            'photo' => ['required', 'image:jpeg,png,jpg,gif,svg', 'max:2048'],
            'location' => ['required', 'string'],
            'market_name' => ['required'],
            'm_category_id' => ['required'],
            'startTime' => ['required', 'date_format:H:i:s'],
            'endTime' => ['required', 'date_format:H:i:s'],
            'device_token' => ['required',],
            'marketType' => ['required',],
            'lang'=> ['required',],
            'lat'=> ['required',],


        ]);

        if ($validator->fails()) {
            return Response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $image_url = Images::SaveUserImage($request);

        $request['password'] = Hash::make($request['password']);

        $market = Market::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'photo' => $image_url,
            'location' => $request->location,
            'market_name' => $request->market_name,
            'm_category_id' => $request->m_category_id,
            'startTime' => $request->startTime,
            'endTime' => $request->endTime,
            'marketType' => $request->marketType,

        ]);

       $market->position()->create(
            [
                'lang'=> $request->lang,
                'lat'=>$request->lat
           ]
        );
        $market->assignRole(['market']);


        $device = new Device();
        $device->device_token = $request->device_token;
        $market->deviceable()->save($device);


        $tokenResult = $market->createToken('personal Access Token')->accessToken;
        $data["market"] = $market;
        $data["tokenType"] = 'Bearer';
        $data["access_token"] = $tokenResult;
        return response()->json($data, Response::HTTP_CREATED);
    }

    public function marketLogin(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'name' => ['required_if:email,null', 'string', Rule::exists('markets')],
                'email' => ['email', Rule::exists('markets')],
                'password' => ['required', 'min:8'],
                'device_token' => ['required',]

            ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if ($request->has('email')) {
            if (auth()->guard('market')->attempt(['email' => request('email'), 'password' => request('password')])) {

                config(['auth.guards.api.provider' => 'market']);

                $market = Market::find(auth()->guard('market')->user()->id);

                $tokenResult = $market->createToken('personal Access Token')->accessToken;
                $data['market'] = $market;
                $data['TokenType'] = 'Bearer';
                $data['Token'] = $tokenResult;
            } else {
                throw new AuthenticationException();
            }
        } else {
            if (auth()->guard('market-api')->attempt(['name' => request('name'), 'password' => request('password')])) {
                config(['auth.guards.api.provider' => 'market-api']);

                $market = Market::find(auth()->guard('market')->user()->id);
                $tokenResult = $market->createToken('personal Access Token')->accessToken;
                $data[' market'] = $market;
                $data["TokenType"] = 'Bearer';
                $data['Token'] = $tokenResult;
            } else {
                throw new AuthenticationException();

            }
        }

        if (!Device::query()->where('deviceable_id', '=', $market->id)
            ->where('deviceable_type', '=', 'App\Models\Market')->exists()) {
            $device = new Device();
            $device->device_token = $request->device_token;
            $market->deviceable()->save($device);

        } else {
            $exist = false;
            $device = Device::query()->where('deviceable_id', '=', $market->id)
                ->where('deviceable_type', '=', 'App\Models\Market')->get();
            foreach ($device as $item) {
                if ($item->device_token == $request->device_token) {
                    $exist = true;
                    break;
                }
            }
            if (!$exist) {
                $device = new Device();
                $device->device_token = $request->device_token;
                $market->deviceable()->save($device);

            }
        }

        return response()->json($data, Response::HTTP_OK);
    }

    public function ProfileUpdate(Request $request): \Illuminate\Http\JsonResponse
    {
        $image_url = Images::SaveUserImage($request);
        Auth::guard('market-api')->user()->update([
            'name' => $request->name,
            'market_name' => $request->market_name,
            'phone' => $request->phone,
            'photo' => $image_url,
            'location' => $request->location,
            'startTime' => $request->startTime,
            'endTime' => $request->endTime,

        ]);

        $market = Market::query()->where('id', '=', Auth::guard('market-api')->id())->get();

        return response()->json($market, Response::HTTP_OK);
    }

    public function MyProfile(): \Illuminate\Http\JsonResponse
    {

        $market = Auth::guard('market-api')->user();

        return response()->json($market);

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

        // find market's email
        $market = Market::query()->where('email', $passwordReset->email)->first();

        // update market password
        $request['password'] = Hash::make($request['password']);

        $market->update($request->only('password'));

        // delete current code
        $passwordReset->delete();

        return response()->json(['message' => 'password has been successfully reset'], Response::HTTP_OK);
    }

    public function verifyAccount(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
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
        $market = Market::query()->where('email', $code->email)->first();

        $date = date("Y-m-d g:i:s");
        $market->email_verified_at = $date;
        $market->save();

        $tokenResult = $market->createToken('personal Access Token')->accessToken;
        $data["message"] = 'Email verified!';
        $data['user'] = $market;
        $data["TokenType"] = 'Bearer';
        $data['Token'] = $tokenResult;
        return response()->json($data, Response::HTTP_OK);

    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        Auth::guard('market-api')->user()->token()->revoke();

        return response()->json("logged out", Response::HTTP_OK);

    }

    public function showAllMarket()
    {
        $res = [];
        $market = Market::query()->get();

        foreach ($market as $item) {
            $friendship = FollowUpRequest::query()->where('user_id', '=', Auth::id())
                ->where('market_id', '=', $item->id)
                ->pluck('request_status');
            $delivery = false;
            if (Delivery::query()->where('market_id', '=', $item->id)->exists()) {
                $delivery = true;

            }
            $result['market'] = $item;
            $result['friendship'] = $friendship;
            $result['delivery_exist'] = $delivery;

            $res[] = $result;
        }
        return response()->json($res, Response::HTTP_OK);
    }

    public function showProductOfMarket(Market $market)
    {
        $product = $market->product()->get();
        return response()->json($product, Response::HTTP_OK);
    }

    public function refreshToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', Rule::exists('markets')],
            'token' => ['required', 'string']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        Notifications::refreshToken($request->id, $request->token, 'market');

        return response()->json('token refreshed', Response::HTTP_OK);
    }

    public function index(M_category $m_category)
    {
        $res = [];
        $market = Market::query()->where('m_category_id', '=', $m_category->id)->get();

        foreach ($market as $item) {
            $friendship = FollowUpRequest::query()->where('user_id', '=', Auth::id())
                ->where('market_id', '=', $item->id)
                ->pluck('request_status');
            $delivery = false;
            if (Delivery::query()->where('market_id', '=', $item->id)->exists()) {
                $delivery = true;

            }
            $result['market'] = $item;
            $result['friendship'] = $friendship;
            $result['delivery_exist'] = $delivery;

            $res[] = $result;
        }
        return response()->json($res, Response::HTTP_OK);

    }

    public function indexMarket()
    {
        $markets = Market::all();

        return view('market.index', compact('markets'));
    }

    public function show($id)
    {
        $market = Market::query()->where('id', '=', $id)->first();
        $category = M_category::query()->where('id', '=', $market->m_category_id)->pluck('name');

        return view('Market.show', compact('market', 'category'));

    }

    public function getNotifications(){
        $market=Auth::guard('market-api')->id();
        $notifications=Notification::query()
            ->join('notifiables','notifications.id','=','notifiables.notification_id')
            ->where('notifiable_id','=',$market)
            ->where('notifiable_type','=','App/Models/Market')
            ->get();
        return response()->json($notifications,Response::HTTP_OK);
    }

    public function getPosition(Market $market){
        $position=Position::query()->where('market_id','=',$market->id)->get();
        return response()->json($position,Response::HTTP_OK);
    }

}
