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
    public function handleCallback(string $provider, Request $request)
{
    try {
        $idToken = $request->input('access_token');  // Get the ID token

        // Step 1: Verify the ID token using Google's tokeninfo API
        $client = new Client();
        $response = $client->get('https://oauth2.googleapis.com/tokeninfo', [
            'query' => ['id_token' => $idToken]  // Use 'id_token' for ID token verification
        ]);

        $userInfo = json_decode($response->getBody(), true);

        if (!isset($userInfo['email'])) {
            throw new Exception('Unable to verify token.');
        }

        // Step 2: Use the user info for login logic
        $user_exist = User::query()->where('email', '=', $userInfo['email'])->first();
        if (!$user_exist) {
            $user_exist = new User();
            $user_exist->name = $userInfo['name'];
            $user_exist->email = $userInfo['email'];
            $user_exist->google_id = $userInfo['sub'];  // Google's unique user ID
            $user_exist->save();
        }

        // Log the user in
        Auth::login($user_exist);

        // Return the access token or other data to the frontend
        return response()->json(['user' => $user_exist, 'access_token' => $idToken]);

    } catch (Exception $exception) {
        return response()->json(['error' => $exception->getMessage()], 400);
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
