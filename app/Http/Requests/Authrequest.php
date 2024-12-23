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
            //'phone'=>'required|unique:users,phone|min:10|max:10',
            //'password'=>'required |min:8|confirmed'
            'phone' => [
                'required',
                'unique:users,phone',
                
                'string',
                'min:12',
                'max:12',
                //'regex:/^\+9639\d{8}$/',  // التحقق من الرقم حسب النمط
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',  // كلمة المرور يجب أن تكون على الأقل 8 محارف
                'regex:/[a-z]/',  // يجب أن تحتوي على حرف صغير
                'regex:/[A-Z]/',  // يجب أن تحتوي على حرف كبير
                'regex:/[0-9]/',  // يجب أن تحتوي على رقم
                'regex:/[!@#$%^&*(),.?":{}|<>]/',  // يجب أن تحتوي على رمز خاص
            ],
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
