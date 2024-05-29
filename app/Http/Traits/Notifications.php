<?php

namespace App\Http\Traits;

use App\Models\Device;
use App\Models\Market;
use App\Models\Notification;
use App\Models\User;

trait Notifications
{
    public static function save($ids, $title, $message, $user_type)
    {
        $notification = Notification::query()->create([
            'title' => $title,
            'body' => $message,
            'is_read' => false
        ]);

        foreach ($ids as $id) {
            if ($user_type == 'market') {
                $market = Market::query()->find($id);
                $market->notifiable()->save($notification);
            } elseif ($user_type == 'user') {
                $user = User::query()->find($id);
                $user->notifiable()->attach($notification->id);
            }
        }


    }

    public static function notify($ids, $title, $message, $user_type)
    {
        $tokens = [];
        if ($user_type == 'market') {

            foreach ($ids as $id) {
                $token = Device::query()->where('deviceable_id', '=', $id)
                    ->where('deviceable_type', '=', 'App\Models\Market')
                    ->pluck('device_token')
                    ->toArray();
                $tokens = array_merge($tokens, $token);
            }
        } elseif ($user_type == 'user') {
            foreach ($ids as $id) {
                $token = Device::query()->where('deviceable_id', '=', $id)
                    ->where('deviceable_type', '=', 'App\Models\User')
                    ->pluck('device_token')
                    ->toArray();
                $tokens = array_merge($tokens, $token);
            }
        }
        // return $tokens;
        // dd($tokens);
        $SERVER_API_KEY = env('SERVER_API_KEY');

        // $token_1 = 'Test Token';

        $data = [

            "registration_ids" => $tokens,

            "notification" => [

                "title" => $title,

                "body" => $message,

                "sound" => "default" // required for sound on ios

            ],

        ];

        $dataString = json_encode($data);

        $headers = [

            'Authorization: key=' . $SERVER_API_KEY,

            'Content-Type: application/json',

        ];

//        CURL library in PHP to send a POST request to the Firebase Cloud Messaging (FCM) API
//        for sending push notifications.
        //1)  Initializes a cURL session and returns a cURL handle ($ch) that will be used for subsequent cURL functions.
        $ch = curl_init();

//       2) Sets the URL to which the cURL request will be sent. In this case, it is set to the FCM API endpoint for sending push notifications.
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

//      3)Sets the request method to POST. This indicates that the cURL request will be a POST request.
        curl_setopt($ch, CURLOPT_POST, true);

//      4)Sets the HTTP headers for the cURL request. The $headers variable should contain the necessary headers, such as the authorization header for FCM.
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

//      5)Disables SSL certificate verification. This is often used when working with self-signed or invalid SSL certificates.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

//      6)Sets the CURLOPT_RETURNTRANSFER option to true,
// which means that the cURL request will return the response as a string instead of outputting it directly.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//      7)Sets the data to be sent in the request body.
// The $dataString variable should contain the payload for the FCM notification.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

//     8) Executes the cURL request and assigns the response to the $response variable.
// The response will be the result of the FCM API call.
        $response = curl_exec($ch);

        Notifications::save($ids, $title, $message, $user_type);

    }

    public static function refreshToken($user_id,$token,$user_type){
        if ($user_type == 'market')
        {
             Device::query()->where('deviceable_id', '=', $user_id)
                ->where('deviceable_type', '=', 'App\Models\Market')
                ->update([
                    'device_token'=>$token
                ]);
        }
        if ($user_type == 'user')
        {
            Device::query()->where('deviceable_id', '=', $user_id)
                ->where('deviceable_type', '=', 'App\Models\User')
                ->update([
                    'device_token'=>$token
                ]);
        }

    }
}
