<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\UserRegisterException;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetUsersRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UserShowRequest;
use App\Models\User;
use App\Services\UserService;
use App\Supports\ResponseSupport;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Return token for registration a new user.
     *
     * @param UserService $service
     * @return JsonResponse
     */
    public function getRegisterToken(UserService $service): JsonResponse
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
     * @param RegisterUserRequest $request
     * @param UserService $service
     * @return JsonResponse
     */
    public function store(RegisterUserRequest $request, UserService $service): JsonResponse
    {
        try {
            $user = $service->store($request, $request->validated());

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

    /**
     * Get all users
     *
     * @param GetUsersRequest $request
     * @param UserService $service
     * @return JsonResponse
     */
    public function index(GetUsersRequest $request, UserService $service): JsonResponse
    {
        try {
            $data = $service->index(
                $request->input('count', 5),
                $request->input('offset', 0),
                $request->input('page', 1),
            );

            return ResponseSupport::success(params: [
                'success' => true,
                ...$data
            ]);

        } catch (UserRegisterException $e) {
            return ResponseSupport::error([
                'success' => false,
                'message' => __('response.' . $e->getMessage())
            ], 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseSupport::error();
        }
    }

    /**
     * Show single user
     *
     * @param UserShowRequest $request
     * @param int $id
     * @param UserService $service
     * @return JsonResponse
     */
    public function show(UserShowRequest $request, int $id, UserService $service)
    {
        try {
            $data = $service->show($id);

            return ResponseSupport::success(params: [
                'success' => true,
                ...$data
            ]);

        } catch (ModelNotFoundException $e) {
            return ResponseSupport::error([
                'success' => false,
                'message' => __('response.'.'The user with the requested identifier does not exist'),
                'fails' => [
                    'user_id' => [
                        __('response.'.'User not found')
                    ]
                ]
            ], 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseSupport::error();
        }
    }
}
