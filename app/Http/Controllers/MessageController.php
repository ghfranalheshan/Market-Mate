<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\Message;
use App\Events\NewMessage;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Models\Message_receipent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;


class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show_my_conversations()
    {
        $user = Auth::guard('api')->user();


        $users1 = Message::query()->where('sendable_id', '=', $user->id)
            ->join('message_receipents', 'messages.id', '=', 'message_id')
            ->join('markets', 'message_receipents.receivable_id', '=', 'markets.id')
            ->select('markets.id', 'markets.name')->get();

        $users2 = Message_receipent::query()->where('receivable_id', '=', $user->id)
            ->join('messages', 'message_receipents.message_id', '=', 'messages.id')
            ->join('markets', 'messages.sendable_id', '=', 'markets.id')
            ->select('markets.id', 'markets.name')->get();


        $users = $users1->merge($users2)->unique('id');


        return response()->json($users, Response::HTTP_OK);

    }

    public function market_show_my_conversations()
    {
        $market = Auth::guard('market-api')->user();

        $users1 = Message::query()->where('sendable_id', '=', $market->id)
            ->join('message_receipents', 'messages.id', '=', 'message_id')
            ->join('users', 'message_receipents.receivable_id', '=', 'users.id')
            ->select('users.id', 'users.name')->get();

        $users2 = Message_receipent::query()->where('receivable_id', '=', $market->id)
            ->join('messages', 'message_receipents.message_id', '=', 'messages.id')
            ->join('users', 'messages.sendable_id', '=', 'users.id')
            ->select('users.id', 'users.name')->get();

        $users = $users1->merge($users2)->unique('id');

        return response()->json($users, Response::HTTP_OK);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreMessageRequest $request
     * @return \Illuminate\Http\jsonResponse
     */
    public function marketSend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => ['required'],
            'message' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $market = Auth::guard('market-api')->user();

        $message = new Message ();
        $message->message = $request->message;

        $market->sendable()->save($message);

        $message_receipent = new Message_receipent ();
        $message_receipent->message_id = $message->id;

        $reciever = User::query()->find($request->receiver_id);
        $reciever->receivable()->save($message_receipent);

        event(new NewMessage($message, $message_receipent));


        return response()->json('created and sent successfully', Response::HTTP_CREATED);

    }

    public function store(StoreMessageRequest $request)
    {
        $user = Auth::guard('api')->user();

        $message = new Message ();
        $message->message = $request->message;

        $user->sendable()->save($message);

        $message_receipent = new Message_receipent ();
        $message_receipent->message_id = $message->id;

        $reciever = Market::query()->find($request->receiver_id);
        $reciever->receivable()->save($message_receipent);

        event(new NewMessage($message, $message_receipent));

        return response()->json('created and sent successfully', Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Message $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        //get messages from specific conversation

        $user = Auth::guard('api')->user();

        $conversation = Message_receipent::query()->where('receivable_id', '=', $id)
            ->join('messages', 'message_receipents.message_id', '=', 'messages.id')
            ->where('messages.sendable_id', '=', $user->id)
            ->orWhere('message_receipents.receivable_id', '=', $user->id)
            ->where('messages.sendable_id', '=', $id)
            ->select('messages.message', 'messages.sendable_id as sender', 'message_receipents.receivable_id as recipient', 'messages.created_at')
            ->get();

        $recipient = Market::query()->where('id', '=', $id)->get();

        $data['messages'] = $conversation;
        $data['sender'] = $user;
        $data['recipient'] = $recipient;

        return response()->json($data);


    }
    public function marketShow($id)
    {
        //get messages from specific conversation

        $market = Auth::guard('market-api')->user();

        $conversation = Message_receipent::query()->where('receivable_id', '=', $id)
            ->join('messages', 'message_receipents.message_id', '=', 'messages.id')
            ->where('messages.sendable_id', '=', $market->id)
            ->orWhere('message_receipents.receivable_id', '=', $market->id)
            ->where('messages.sendable_id', '=', $id)
            ->select('messages.message', 'messages.sendable_id as sender', 'message_receipents.receivable_id as recipient', 'messages.created_at')
            ->get();

        $recipient = User::query()->where('id', '=', $id)->get();

        $data['messages'] = $conversation;
        $data['sender'] = $market;
        $data['recipient'] = $recipient;

        return response()->json($data);


    }

}
