<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Contracts\UserInterface;
use App\Http\Requests\AuthRequest;
use App\Http\Resources\AuthResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Contracts\Providers\Auth;

class AuthController extends Controller
{
    public $userInterface;
    public function __construct(UserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }

    public function register(AuthRequest $request)
    {
        // Validate the request data, including password confirmation
        $validatedUserData = $request->validated();
        // dd($validatedUserData);

        // Hash the password
        $validatedUserData['password'] = Hash::make($validatedUserData['password']);
        // dd($validatedUserData['password']);
        // Assign the role based on the request
        switch (strtolower($request->role)) {
            case Config::get('variable.ADMIN'):
                $validatedUserData['role'] = Config::get('variable.TWO');
                // dd($validatedUserData['role']);
                break;
            case Config::get('variable.OWNER'):
                $validatedUserData['role'] = Config::get('variable.THREE');
                // dd($validatedUserData['role']);
                break;
            case Config::get('variable.RIDER'):
                $validatedUserData['role'] = Config::get('variable.FOUR');
                break;
            case Config::get('variable.DRIVER'):
                $validatedUserData['role'] = Config::get('variable.FIVE');
                break;
            default:
                $validatedUserData['role'] = Config::get('variable.ONE');
                // dd($validatedUserData['role'] );

                break;
        }
        // dd($validatedUserData);

        // Store the user data
        $user = $this->userInterface->store('User', $validatedUserData);
        // dd($user);
         // Generate a JWT token for the newly registered user
        $token = JWTAuth::fromUser($user);
        // Generate a token for the user
        // $token = $user->createToken('rideandsavor')->plainTextToken;

        // Return the user data and token in the response
        if ($request->expectsJson()) {
            return response()->json(
                 new AuthResource($user, $token)); // 201 Created
        }
    }

    public function login(Request $request)
    {
        $userData = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6'
        ]);
        if ($token = JWTAuth::attempt($userData)) {
            $user = User::find(auth()->user()->id);
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_role' => $user->roleInfo,
                'message' => Config::get('variable.LOGIN_SUCCESSFULLY'),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }
        return response()->json([
            'message' => Config::get('variable.INVALID_USERNAME_ADN_PASSWORD')
        ], Config::get('variable.CLIENT_ERROR'));
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 200);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_no' => $user->phone_no,
            'gender' => $user->gender,
            'age' => $user->age
        ], 200);
    }


    // public function logout()
    // {
    //     $user = User::find(auth()->user()->id);
    //     if (!$user) {
    //         return response()->json([
    //             'message' => Config::get('variable.NO_AUTHENTICATED_USER')
    //         ], Config::get('variable.CLIENT_ERROR'));
    //     }
    //     $user->tokens->each(function ($token) {
    //         $token->delete();
    //     });
    //     return response()->json([
    //         'message' => Config::get('variable.LOGGED_OUT_SUCCESSFULLY')
    //     ], Config::get('variable.OK'));
    // }

    public function logout()
{
    $token = JWTAuth::getToken();
    if (!$token) {
        return response()->json(['error' => 'Token not provided'], 401);
    }

    // Invalidate the token
    JWTAuth::invalidate($token);
    return response()->json([
        'message' => Config::get('variable.LOGGED_OUT_SUCCESSFULLY')
    ], Config::get('variable.OK'));
}

}
