<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\UserRegisterException;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
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
                'success' => true,
                'token' => $token
            ]);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseSupport::error();
        }
    }

    /**
     * Register new user
     *
     * @param UserRegistrationService $service
     * @return JsonResponse
     */
    public function registerUser(RegisterUserRequest $request, UserRegistrationService $service): JsonResponse
    {
        try {
            $user = $service->registerUser($request, $request->validated());

            return ResponseSupport::success(params: [
                'success' => true,
                'user_id' => $user->id,
                'message' => __('response.' . 'New user successfully registered.'),
            ]);

        } catch (UserRegisterException $e) {
            return ResponseSupport::error([
                'success' => false,
                'message' => __('response.' . $e->getMessage())
            ], 409);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseSupport::error();
        }
    }
}
