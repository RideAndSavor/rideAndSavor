<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use GuzzleHttp\Client;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\SocialLoginRequest;

class SocialLoginController extends Controller
{
    // Redirect to Google 
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle the callback from (Google,Facebook)
    public function handleCallback(SocialLoginRequest $request): UserResource
    {
        try {
            $accessToken = $request->input('access_token');
            $provider = $request->input('provider');
            // $user = Socialite::driver('google')->stateless()->user();
            // dd($user);
            // dd($accessToken, $provider);
            //ya29.a0AcM612z84IAfEvNyP1cuW2TSvs4x7uofXGHcV2JKNeQFuGiazqC7bdAP0AxXNNN-3jvkn_N3yf_GRTN9lq7lYJhuM7nQImsWZzqrwiQK8cvY1apGBMNzv_Ny26KmxjPrb0Xn_yCOEAEXtJcsv4wT7XQycoVKicCJswEaCgYKAYsSARESFQHGX2MiOTp4s-3EYLNLIx8x8o_GEA0170(google access_token)
            //EAB3l6mo5V2wBOZBs466GYVncMJTUwl7iPowbgZAyAiLNY26zGXQeaaojiRCNpoYm5paPtHZAMbduI7KmcE6523gX02Gigf2ZBVkivI31JMZBZAfuMn8Vzf5wQPIrejr6THOjZCq6R8V6Hzn0wtz7hZChQZBZBHQvpfoA4F8FZB0Qr5GJ3l5Uoc6nAKeIPLpiG3L6NVGZCCJnAGyrAdO92QGE4atF6VHkGWkZD(facebook access_token)

            $user = Socialite::driver($provider)->stateless()->userFromToken($accessToken);
            $data = $this->findOrCreate($user, $provider);
            return new UserResource($data->setAttribute('token', $user->token));

        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode());
        }
    }

    private function findOrCreate(object $user, string $provider): User
    {
        $userExist = User::query()->where('email', '=', $user->email)->first();
        if (!$userExist) {
            $userExist = new User();
            $userExist->name = $user->name;
            $userExist->email = $user->email;
            if ($provider === 'google') {
                $userExist->google_id = $user->id;
            } else {
                $userExist->facebook_id = $user->id;
            }
            $userExist->save();
        }
        Auth::login($userExist);
        return $userExist;
    }

}
