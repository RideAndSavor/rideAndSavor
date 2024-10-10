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
        // try {
        //     // $user = Socialite::driver('google')->stateless()->user();
        //     // dd($user);


        //     $accessToken = $request->input('access_token');
        //     $provider = $request->input('provider');
        //     // dd($accessToken, $provider);
        //     //ya29.a0AcM612z84IAfEvNyP1cuW2TSvs4x7uofXGHcV2JKNeQFuGiazqC7bdAP0AxXNNN-3jvkn_N3yf_GRTN9lq7lYJhuM7nQImsWZzqrwiQK8cvY1apGBMNzv_Ny26KmxjPrb0Xn_yCOEAEXtJcsv4wT7XQycoVKicCJswEaCgYKAYsSARESFQHGX2MiOTp4s-3EYLNLIx8x8o_GEA0170(google access_token)
        //     //EAB3l6mo5V2wBOZBs466GYVncMJTUwl7iPowbgZAyAiLNY26zGXQeaaojiRCNpoYm5paPtHZAMbduI7KmcE6523gX02Gigf2ZBVkivI31JMZBZAfuMn8Vzf5wQPIrejr6THOjZCq6R8V6Hzn0wtz7hZChQZBZBHQvpfoA4F8FZB0Qr5GJ3l5Uoc6nAKeIPLpiG3L6NVGZCCJnAGyrAdO92QGE4atF6VHkGWkZD(facebook access_token)

        //     $user = Socialite::driver($provider)->stateless()->userFromToken($accessToken);
        //     $data = $this->findOrCreate($user, $provider);
        //     return new UserResource($data->setAttribute('token', $user->token));

        // } catch (Exception $exception) {
        //     throw new Exception($exception->getMessage(), $exception->getCode());
        // }


        try {
            $idToken = $request->input('access_token'); // Get the ID token from the request
            $provider = $request->input('provider');

            // Step 1: Verify the ID token using Google's token verification API
            $client = new Client();
            $response = $client->get('https://oauth2.googleapis.com/tokeninfo', [
                'query' => ['token' => $idToken]
            ]);

            $userInfo = json_decode($response->getBody(), true);

            if (!isset($userInfo['email'])) {
                throw new Exception('Unable to verify token.');
            }

            // Step 2: Find or create the user in the database
            $user_exist = User::query()->where('email', '=', $userInfo['email'])->first();
            if (!$user_exist) {
                $user_exist = new User();
                $user_exist->name = $userInfo['name'];
                $user_exist->email = $userInfo['email'];
                if ($provider === 'google') {
                    $user_exist->google_id = $userInfo['sub'];  // Google's unique user ID
                }
                $user_exist->save();
            }

            // Step 3: Log the user in
            Auth::login($user_exist);

            // Optionally, include a token if needed
            //array_merge($user_exist->toArray(), ['token' => $idToken]);

            return new UserResource($user_exist->setAttribute('token', $idToken));

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
