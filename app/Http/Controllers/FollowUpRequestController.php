<?php

namespace App\Http\Controllers;

use App\Models\FollowUpRequest;
use App\Http\Requests\StoreFollowUpRequestRequest;
use App\Http\Requests\UpdateFollowUpRequestRequest;
use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class FollowUpRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return
     */
    //send folow up request from user
    //user-----
    public function followUpRequset(Request $request): \Illuminate\Http\jsonResponse
    {
        $validator = Validator::make($request->all(), [
            'market_id' => ['required']
        ]);
        if ($validator->fails()) {
            return Response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $followUP = FollowUpRequest::query()->create(
            [
                'market_id' => $request->market_id,
                'user_id' => Auth::guard('api')->id(),
            ]
        );

        return Response()->json('follow sent successfully', Response::HTTP_CREATED);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreFollowUpRequestRequest $request
     * @return \Illuminate\Http\Response
     */
    public function cancelfollowUpRequset(FollowUpRequest $followUpRequest): \Illuminate\Http\jsonResponse
    {
        $followUpRequest->delete();

        return Response()->json('the request cancel successfully ', Response::HTTP_OK);

    }

    public function showFollowing(): \Illuminate\Http\jsonResponse
    {
        $markets = FollowUpRequest::query()->where('user_id', '=', Auth::guard('api')->id())
            ->where('request_status', '=', 'accepted')
            ->join('markets','follow_up_requests.market_id','=','markets.id')
           ->select('markets.*')
            ->get();


        return Response()->json($markets, Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\FollowUpRequest $followUpRequest
     * @return \Illuminate\Http\Response
     */

    //show the list of followers
    //market----------
    public function showMyFollowers(): \Illuminate\Http\jsonResponse
    {
        $followUpRequest = FollowUpRequest::query()->where('market_id', '=', Auth::guard('market-api')->id())
            ->where('request_status', '=', 'accepted')->get();

        return Response()->json($followUpRequest, Response::HTTP_OK);
    }

    public function showMyFollowRequest(): \Illuminate\Http\jsonResponse
    {
        $followUpRequest = FollowUpRequest::query()->where('market_id', '=', Auth::guard('market-api')->id())
            ->where('request_status', '=', 'waiting')->get();
        return Response()->json($followUpRequest, Response::HTTP_OK);
    }


    public function acceptFollowUPRequset(FollowUpRequest $followUpRequest): \Illuminate\Http\jsonResponse
    {
        $followUpRequest->update(
            [
                'request_status' => 'accepted'
            ]
        );

        return Response()->json('follow Up Request accepted', Response::HTTP_OK);

    }

    public function rejectFollowUpRequset(FollowUpRequest $followUpRequest): \Illuminate\Http\jsonResponse
    {
        if($followUpRequest->request_status == 'waiting')
        $followUpRequest->update(
            [
                'request_status' => 'rejected'
            ]
        );

        return Response()->json('follow Up Request rejected', Response::HTTP_OK);

    }

}
