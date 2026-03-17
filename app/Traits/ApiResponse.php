<?php

namespace App\Traits;

trait ApiResponse
{
    protected function success($data = null, string $message = 'تمت العملية بنجاح', int $code = 200)
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], $code, [], JSON_UNESCAPED_UNICODE);
    }

    protected function created($data = null, string $message = 'تم الإنشاء بنجاح')
    {
        return $this->success($data, $message, 201);
    }

    protected function error(string $message = 'حدث خطأ', int $code = 400, $errors = null)
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'errors'  => $errors,
        ], $code, [], JSON_UNESCAPED_UNICODE);
    }

    protected function unauthorized(string $message = 'غير مصرح لك')
    {
        return $this->error($message, 401);
    }

    protected function forbidden(string $message = 'ليس لديك صلاحية')
    {
        return $this->error($message, 403);
    }

    protected function notFound(string $message = 'غير موجود')
    {
        return $this->error($message, 404);
    }

    protected function validationError($errors, string $message = 'بيانات غير صالحة')
    {
        return $this->error($message, 422, $errors);
    }
}