<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Authrequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone'=>'required|unique:users,phone|min:10|max:10',
            'password'=>'required |min:8|confirmed'
        ];
    }

    public function attributes()
    {
        return [
            'phone'=>__('main.phone'),
            'password'=>__('main.password'),
            'password_confirmation'=>__('main.password_confirmation'),
        ];
    }
}
