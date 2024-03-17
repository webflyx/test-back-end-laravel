<?php

namespace App\Services;

use App\Exceptions\PositionException;
use App\Http\Resources\PositionResource;
use App\Models\Position;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PositionService
{
    /**
     * @return AnonymousResourceCollection
     * @throws PositionException
     */
    public function index(): AnonymousResourceCollection
    {
        $positions = Position::all();
        if($positions->count() === 0){
            throw new PositionException('Positions not found');
        }
        return PositionResource::collection($positions);
    }
}
