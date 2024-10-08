<?php

namespace App\Http\Controllers;

use Response;
use Exception;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\ResponseFactory;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    // Redirect to Google 
    // public function redirectToGoogle()
    // {
    //     return Socialite::driver('google')->redirect();
    // }

    // Handle the callback from (Google,Facebook)
    public function handleCallback(string $provider): Response|ResponseFactory|UserResource
    {
        try {
            $user = Socialite::driver($provider)->stateless()->user();
            $user_exist = User::query()->where('email', '=', $user->email)->first();
            if (!$user_exist) {
                $user_exist = new User();
                $user_exist->name = $user->name;
                $user_exist->email = $user->email;
                if ($provider === 'google') {
                    $user_exist->google_id = $user->id;
                } else {
                    $user_exist->facebook_id = $user->id;
                }
                $user_exist->save();
            }
            Auth::login($user_exist);
            array_merge($user_exist, ['token' => $user->token]);
            return new UserResource($user_exist);

        } catch (Exception $exception) {
            return response($exception->getMessage(), $exception->getCode());

        }

    }

}
