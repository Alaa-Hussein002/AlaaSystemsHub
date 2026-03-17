<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|min:2|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'الاسم مطلوب',
            'name.min'           => 'الاسم قصير جداً',
            'email.required'     => 'البريد الإلكتروني مطلوب',
            'email.email'        => 'البريد الإلكتروني غير صالح',
            'email.unique'       => 'البريد الإلكتروني مسجل مسبقاً',
            'password.required'  => 'كلمة المرور مطلوبة',
            'password.min'       => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => false,
                'message' => 'بيانات غير صالحة',
                'errors'  => $validator->errors(),
            ], 422, [], JSON_UNESCAPED_UNICODE)
        );
    }
}