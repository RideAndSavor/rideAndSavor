<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    // Redirect to Google 
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle the callback from Google 
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();
            $user_exist = User::where('email', '=', $user->email)->first();
            if (!$user_exist) {
                $new_user = new User();
                $new_user->name = $user->name;
                $new_user->email = $user->email;
                $new_user->google_id = $user->id;
                $new_user->save();
            }

            Auth::login($user_exist);
            return response()->json([
                'status' => 200,
                'token' => $user->token,
                'name' => $user_exist->name,
                'email' => $user_exist->email,
                'role' => $user->role == 0 ? 'user' : $user->role,
            ]);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode());

        }

    }

    // Redirect to Facebook 
    public function redirectToFacebook(): RedirectResponse
    {
        return Socialite::driver('facebook')->redirect();
    }

    // Handle the callback from Facebook 
    public function handleFacebookCallback()
    {
        try {
            $user = Socialite::driver('facebook')->stateless()->user();
            $user_exist = User::where('email', '=', $user->email)->first();
            if (!$user_exist) {
                $new_user = new User();
                $new_user->name = $user->name;
                $new_user->email = $user->email;
                $new_user->facebook_id = $user->id;
                $new_user->save();
            }

            Auth::login($user_exist);
            return response()->json([
                'status' => 200,
                'token' => $user->token,
                'name' => $user_exist->name,
                'email' => $user_exist->email,
                'role' => $user->role == 0 ? 'user' : $user->role,
            ]);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode());

        }

    }

}
