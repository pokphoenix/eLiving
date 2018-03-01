<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tel' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',

            'job_title' => 'required|string|max:255',
            'residence_name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:domains,name',
            'company_name' => 'required|string|max:255',
            'unit' => 'required|numeric',
            'agree' => 'required',
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'first_name.required' => 'กรุณากริอก',
    //         'amount.required'  => 'A amount is required',
    //         'amount.numeric'  => 'A amount is number only',
    //         'exchange_rate.required'  => 'A exchange rate is required',
    //         'exchange_rate.between'  => 'A exchange rate is between 0 - 99.99',
    //         'bank_free.required'  => 'A bank free is required',
    //         'bank_free.numeric'  => 'A bank free is number only',
    //         'channel_id.required'  => 'A channel  is required',
    //         'remark.required'  => 'A remark  is required',
    //         'image.required'  => 'A image  is required',
    //     ];
    // }
}
