<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6|regex:/^[0-9]{6}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صالح',
            'email.exists' => 'البريد الإلكتروني غير مسجل',
            'otp.required' => 'رمز التحقق مطلوب',
            'otp.size' => 'رمز التحقق يجب أن يكون 6 أرقام',
            'otp.regex' => 'رمز التحقق يجب أن يحتوي على أرقام فقط',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'بيانات غير صالحة',
                'errors' => $validator->errors(),
            ], 422, [], JSON_UNESCAPED_UNICODE)
        );
    }
}