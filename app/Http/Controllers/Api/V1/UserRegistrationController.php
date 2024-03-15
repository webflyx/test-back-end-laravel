<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\UserRegistrationService;
use App\Supports\ResponseSupport;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UserRegistrationController extends Controller
{
    /**
     * Return token for registration a new user.
     *
     * @param UserRegistrationService $service
     * @return JsonResponse
     */
    public function getRegisterToken(UserRegistrationService $service): JsonResponse
    {
        try {
            $token = $service->getRegisterToken();

            return ResponseSupport::success(params: [
                'token' => $token
            ]);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseSupport::error();
        }
    }

}
