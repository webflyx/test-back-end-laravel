<?php

namespace App\Services;

use App\Exceptions\UserRegisterException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRegistrationService
{
    /**
     * @return string
     */
    public function getRegisterToken(): string
    {
        $token = Str::random(50);

        DB::table('user_registration_tokens')->insert([
            'token' => $token,
            'created_at' => now(),
            'expired_at' => now()->addMinutes(40),
        ]);

        return $token;
    }

    /**
     * @param Request $request
     * @param $data
     * @return User
     * @throws UserRegisterException
     */
    public function registerUser(Request $request, $data): User
    {
        if (User::where('email', $data['email'])->orWhere('phone', $data['phone'])->first()) {
            throw new UserRegisterException('User with this phone or email already exist.');
        }

        //TODO: TINYPNG
        $data['photo'] = $request->file('photo')->store('user-photo', 'public');
        DB::table('user_registration_tokens')->where('token', $request->header('Token'))->delete();

        return User::create($data);
    }
}
