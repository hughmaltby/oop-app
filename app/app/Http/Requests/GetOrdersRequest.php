<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetOrdersRequest extends FormRequest
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
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'customer' => 'required|in:W,X,Y,Z'
        ];
    }

    public function messages()
    {
        return [
            'customer.required' => 'You must specify a customer.',
            'customer.in' => 'Customer must be in array [\'W\', \'X\', \'Y\', \'Z\'].',
        ];
    }
}
