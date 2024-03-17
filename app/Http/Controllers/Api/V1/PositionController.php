<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\PositionException;
use App\Http\Controllers\Controller;
use App\Services\PositionService;
use App\Supports\ResponseSupport;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PositionController extends Controller
{
    /**
     * Get users positions
     *
     * @param PositionService $service
     * @return JsonResponse
     */
    public function index(PositionService $service): JsonResponse
    {
        try {
            $positions = $service->index();

            return ResponseSupport::success(params: [
                'success' => true,
                'positions' => $positions
            ]);

        } catch (PositionException $e) {
            return ResponseSupport::error([
                'success' => false,
                'message' => __('response.' . $e->getMessage())
            ], 422);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseSupport::error();
        }
    }
}
