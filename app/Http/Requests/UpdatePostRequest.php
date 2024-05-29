<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdatePostRequest extends FormRequest
{

    public function authorize()
    {
//        if($post->market_id != Auth::guard('market-api')->id()){
//            return false;
//        }
        return true;
    }


    public function rules()
    {
        return [
            'text' => ['required_without_all:images','string'],
            'photo' => ['array'],
            'photo.*' => ['file','mimes:png,jpeg,svg,bmp,jpg'],
            'language' => ['required','string'],
        ];
    }
}
