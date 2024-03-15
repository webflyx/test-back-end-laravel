<?php

namespace App\Supports;

use Illuminate\Http\JsonResponse;

class ResponseSupport
{
    /**
     * @param string $message
     * @param array $params
     * @param int $code
     * @return JsonResponse
     */
    public static function success(string $message = '', array $params = [], int $code = 200): JsonResponse
    {
        $data = [
            ...$params,
        ];

        if($message){
            $data['message'] = __('response.'.$message);
        }

        return response()->json($data, $code);
    }

    /**
     * @param string $error
     * @param int $code
     * @return JsonResponse
     */
    public static function error(string $error = 'Something went wrong.', int $code = 400): JsonResponse
    {
        return response()->json([
                'errors' => [
                    'error'=> __('response.'.$error)
                ],
        ], $code);
    }
}
