<?php

namespace App\Http\Controllers;

use Exception;
use Google_Client;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\SocialLoginRequest;
class SocialLoginController extends Controller
{
    // Redirect to Google for OAuth consent
    public function redirectToGoogle()
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URL'));
        $client->setAccessType('offline'); // Request refresh token
        $client->setPrompt('consent');
        $client->addScope('https://www.googleapis.com/auth/userinfo.profile');
        $client->addScope('openid');

        // Generate the Google OAuth URL
        $authUrl = $client->createAuthUrl();

        return response()->json(['url' => $authUrl]);
    }

    // Handle the OAuth callback
    public function handleGoogleCallback(SocialLoginRequest $request)
    { 
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URL'));

        // Get the code from the query parameter
        $code = $request->input('code');
        $provider = $request->input('provider');

        try { 
            // Exchange the authorization code for an access token
            $token = $client->fetchAccessTokenWithAuthCode($code);
            // Set the access token on the client
            $client->setAccessToken($token);

            // Fetch the user information using the access token
            $userData = $this->getUserData($token['access_token']);
            dd($userData);
            $data = $this->findOrCreate($userData, $provider);
            dd($data);
            if ($request->expectsJson()) {
                return new UserResource($data->setAttribute('token', $token));
            }
            return redirect('https://www.dailyfairdeal.com');
        } 
        catch (Exception $e) {
            error_log('Error during Google OAuth: ' . $e->getMessage());
            return redirect('/')->with('error', 'Failed to authenticate with Google.');
        }
    }

    // Helper function to fetch user profile data using access token
    private function getUserData($accessToken)
    {
        // Make a request to Google to get user profile information
        $response = Http::get('https://www.googleapis.com/oauth2/v3/userinfo', [
            'access_token' => $accessToken,
        ]);

        return json_decode($response->getBody()->getContents(), false);
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
