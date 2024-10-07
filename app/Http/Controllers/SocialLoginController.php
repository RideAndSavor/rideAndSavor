<?php

namespace App\Http\Controllers;

use Response;
use Exception;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\ResponseFactory;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    // Redirect to Google 
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle the callback from Google 
    public function handleGoogleCallback(): Response|ResponseFactory|UserResource
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();
            $user_exist = User::where('email', '=', $user->email)->first();
            if (!$user_exist) {
                $user_exist = new User();
                $user_exist->name = $user->name;
                $user_exist->email = $user->email;
                $user_exist->google_id = $user->id;
                $user_exist->save();
            }

            Auth::login($user_exist);
            array_merge($user_exist, ['token' => $user->token]);
            return new UserResource($user_exist);
            // return response()->json([
            //     'token' => $user->token,
            //     'name' => $user_exist->name,
            //     'email' => $user_exist->email,
            //     'role' => $user->role == 0 ? 'user' : $user->role,
            // ], 200);
        } catch (Exception $exception) {
            return response($exception->getMessage(), $exception->getCode());

        }

    }

    // Redirect to Facebook 
    public function redirectToFacebook(): RedirectResponse
    {
        return Socialite::driver('facebook')->redirect();
    }

    // Handle the callback from Facebook 
    public function handleFacebookCallback(): Response|ResponseFactory|UserResource
    {
        try {
            $user = Socialite::driver('facebook')->stateless()->user();
            $user_exist = User::where('email', '=', $user->email)->first();
            if (!$user_exist) {
                $user_exist = new User();
                $user_exist->name = $user->name;
                $user_exist->email = $user->email;
                $user_exist->facebook_id = $user->id;
                $user_exist->save();
            }

            Auth::login($user_exist);
            array_merge($user_exist, ['token' => $user->token]);
            return new UserResource($user_exist);
            // return response()->json([
            //     'token' => $user->token,
            //     'name' => $user_exist->name,
            //     'email' => $user_exist->email,
            //     'role' => $user->role == 0 ? 'user' : $user->role,
            // ], 200);
        } catch (Exception $exception) {
            return response($exception->getMessage(), $exception->getCode());

        }

    }

}
