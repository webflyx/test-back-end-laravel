<?php

namespace App\Http\Middleware;

use App\Supports\ResponseSupport;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RegisterValidToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $tokenData = DB::table('user_registration_tokens')->where('token', $request->header('Token'))->first();

        if (!$request->header('Token')) {

            return ResponseSupport::error([
                'success' => false,
                'message' => __('response.'.'Token is empty.')
            ]);

        } elseif (!$tokenData) {

            return ResponseSupport::error([
                'success' => false,
                'message' => __('response.'.'Token is invalidate.')
            ]);

        } elseif ($tokenData->expired_at < now()) {

            return ResponseSupport::error([
                'success' => false,
                'message' => __('The token expired.')
            ], 401);
        }


        return $next($request);
    }
}
