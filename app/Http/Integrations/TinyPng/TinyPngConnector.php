<?php

namespace App\Http\Integrations\TinyPng;

use App\Supports\ResponseSupport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Tinify\Tinify;

class TinyPngConnector
{

    public function __construct()
    {
        Tinify::setKey(env('TINY_API', false),);
    }

    /**
     * Set file name
     *
     * @param $image
     * @return string
     */
    private function setFilename($image): string
    {
        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $fileExtension = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
        return $originalFilename . '-opt-' . now()->timestamp . '.' . $fileExtension;
    }

    /**
     * Optimization and store image
     *
     * @param $image
     * @param string $path
     * @return string|JsonResponse
     */
    public function store($image, string $path = 'public/'): string|JsonResponse
    {
        try {
            $filename = $this->setFilename($image);

            $sourceData = \Tinify\fromFile($image);
            $sourceData->toFile(Storage::path($path) . $filename);

            return str_replace('public/', '', $path) . $filename;

        } catch (\Exception $e) {
            return ResponseSupport::error($e->getMessage());
        }
    }

}
