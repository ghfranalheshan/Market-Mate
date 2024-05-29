<?php

namespace App\Http\Requests;

use App\Models\Delivery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateDeliveryRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @param Delivery $delivery
     * @return array<string, mixed>
     */
    public function rules()
    {
        $delivery = request()->route()->parameter('delivery');

        return [
            'name'=>['required','string',Rule::unique('deliveries')->ignore($delivery->id)],
            'email' => ['required', 'email', Rule::unique('deliveries')->ignore($delivery->id)],
            'password' => ['required', 'min:8'],
            'c_password' => ['required', 'same:password'],
            'phone' => ['required', 'string'],
            'hour_number'=>['required'],
        ];
    }
}
