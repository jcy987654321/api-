<?php

namespace App\Services;

abstract class BaseService
{
    protected function success($data = [], $message = 'success', $code = 200)
    {
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
    }

    protected function error($message = 'error', $code = 400, $data = [])
    {
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
    }
}
