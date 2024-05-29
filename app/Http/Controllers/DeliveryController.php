<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Http\Requests\StoreDeliveryRequest;
use App\Http\Requests\UpdateDeliveryRequest;
use App\Models\Market;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class DeliveryController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDeliveryRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreDeliveryRequest $request){

        $request['password'] = Hash::make($request['password']);

        $delivery=Delivery::query()->create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->password,
            'phone'=>$request->phone,
            'hour_number'=>$request->hour_number,
            'market_id'=>Auth::id(),
        ]);

        $delivery->assignRole(['delivery']);

        return response()->json($delivery,Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Delivery  $delivery
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Delivery $delivery)
    {
        return response()->json($delivery,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDeliveryRequest  $request
     * @param  \App\Models\Delivery  $delivery
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateDeliveryRequest $request, Delivery $delivery)
    {
        if(Auth::guard('market-api')->id() != $delivery->market_id){
            return response()->json('UNAUTHORIZED',Response::HTTP_UNAUTHORIZED);
        }

        $request['password'] = Hash::make($request['password']);

        $delivery->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->password,
            'phone'=>$request->phone,
            'hour_number'=>$request->hour_number,
        ]);


        return response()->json($delivery,Response::HTTP_OK);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Delivery  $delivery
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Delivery $delivery)
    {
        if (Auth::guard('market-api')->id() != $delivery->market_id){
            return response()->json('UNAUTHORIZED',Response::HTTP_UNAUTHORIZED);
        }
        $delivery->delete();

        return response()->json('delivery man account deleted successfully', Response::HTTP_OK);
    }

    //delivery man auth operation


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'name' => ['required_if:email,null', 'string', Rule::exists('deliveries')],
                'email' => ['email', Rule::exists('deliveries')],
                'password' => ['required', 'min:8'],
            ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if($request->has('email')){
            if (auth()->guard('delivery')->attempt(['email' => request('email'), 'password' => request('password')])) {
                config(['auth.guards.api.provider' => 'delivery']);

                $delivery = Delivery::find(auth()->guard('delivery')->user()->id);

                $tokenResult = $delivery->createToken('personal Access Token')->accessToken;
                $data['delivery'] =  $delivery;
                $data['TokenType'] = 'Bearer';
                $data['Token'] = $tokenResult;
            } else
            {
                throw new AuthenticationException();
            }
        }
        else{
            if (auth()->guard('delivery')->attempt(['name' => $request->name, 'password' => $request->password ])) {
                config(['auth.guards.api.provider' => 'delivery']);

                $delivery = Delivery::find(auth()->guard('delivery')->user()->id);
                $tokenResult = $delivery->createToken('personal Access Token')->accessToken;
                $data['delivery'] =  $delivery;
                $data["TokenType"] = 'Bearer';
                $data['Token'] = $tokenResult;
            } else {
                throw new AuthenticationException();

            }
        }

        return response()->json($data, Response::HTTP_OK);
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        Auth::guard('delivery-api')->user()->token()->revoke();

        return response()->json("logged out", Response::HTTP_OK);

    }
}
