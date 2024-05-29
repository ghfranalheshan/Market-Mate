<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CodeCheckController extends Controller
{
    public function RequestCode (Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email'
        ]);
        // Delete all old code that user send before.
        ResetCodePassword::query()->where('email', $request->email)->delete();

        // Generate random code
        $code=substr(number_format(rand(), 0, '', ''), 0, 6);
        $data['code'] = $code;
        $data['email'] = $request->email;

        // Create a new code
        $codeData = ResetCodePassword::query()->create($data);

        // Send email to user

        $user=User::query()->where('email',$request->email)->first();
        $market=Market::query()->where('email',$request->email)->first();
        if($user!= null && Auth::guard('api')->id()==$user->id) {
            $user->sendCodeByEmail($code);
        }
        else if($market!= null && Auth::guard('market-api')->id()==$market->id)
        {
            $market->sendCodeByEmail($code);
        }

            return response()->json(['code has been sent to user email'], Response::HTTP_OK);
    }

    public function checkCode(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'code' => 'required|string|exists:password_resets',
        ]);

        // find the code
        $passwordReset = ResetCodePassword::query()->where('code', $request->code)->first();

        // check if it is not expired : the time is one hour
        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();
            return response()->json(['message' => 'passwords code has expired'],Response::HTTP_UNPROCESSABLE_ENTITY );
            //422 Unprocessable Content
        }

        return response()->json([
            'message' => 'passwords code is valid'
        ], Response::HTTP_OK);
    }



}
