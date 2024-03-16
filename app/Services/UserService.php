<?php

namespace App\Services;

use App\Exceptions\UserRegisterException;
use App\Facades\TinyPngFacade;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserService
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
    public function store(Request $request, $data): User
    {
        if (User::where('email', $data['email'])->orWhere('phone', $data['phone'])->first()) {
            throw new UserRegisterException('User with this phone or email already exist.');
        }

        $photo = TinyPngFacade::store($request->file('photo'), 'public/user-photo/');
        $data['photo'] = $photo;

        DB::table('user_registration_tokens')->where('token', $request->header('Token'))->delete();

        return User::create($data);
    }

    /**
     * @param int $count
     * @param int $offset
     * @param int $page
     * @return array
     * @throws UserRegisterException
     */
    public function index(int $count, int $offset, int $page): array
    {
        $skipByPage = 0;
        if($page - 1 > 0) {
            $skipByPage = ($page - 1) * $count;
        }

        $users = User::all();
        $usersCount = $users->count();
        $users = UserResource::collection($users->skip($offset + $skipByPage)->take($count));

        $totalPages = ceil($usersCount / $count);
        $nextPage = $page + 1;
        $prevPage = $page - 1;

        $currentUrl = url()->current();
        $nextUrl = $currentUrl . '?page=' . $nextPage . '&offset=' . $offset . '&count=' . $count;
        $prevUrl = $currentUrl . '?page=' . $prevPage . '&offset=' . $offset . '&count=' . $count;

        if($prevPage < 1){
            $prevUrl = null;
        }

        if ($nextPage > $totalPages) {
            $nextUrl = null;
        }

        if ($page > $totalPages) {
            throw new UserRegisterException('Page not found.');
        }

        $data = [
            'page' => $page,
            'total_pages' => $totalPages,
            'total_users' => $usersCount,
            'count' => $count,
            'links' => [
                'next_url' =>$nextUrl,
                'prev_url' => $prevUrl,
            ],
            'users' => $users,
        ];

        return $data;
    }
}
