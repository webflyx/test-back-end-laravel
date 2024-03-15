<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRegistrationService
{
    /**
     * @return string
     */
    public function getRegisterToken(): string
    {
        $token = Str::random();

        DB::table('user_registration_tokenss')->insert([
            'token' => $token,
            'created_at' => now(),
            'expired_at' => now()->addMinutes(40),
        ]);

        return $token;
    }

}
