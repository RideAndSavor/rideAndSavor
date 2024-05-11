<?php

namespace App\Http\Controllers;

use App\Contracts\UserInterface;
use App\Http\Requests\AuthRequest;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public $userInterface;
    public function __construct(UserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }

    public function register(AuthRequest $request)
    {
        $validatedUserData = $request->validated();
        $validatedUserData['password'] = Hash::make($request->password);
        $userEamil = User::where('email', $request->email)->first();
        if ($userEamil) {
            return response()->json([
                'message' => Config::get('variable.USER_EMAIL_ALREADY_EXIT')
            ]);
        }
        switch (strtolower($request->role)) {
            case Config::get('variables.ADMIN'):
                $validatedUserData['role'] = Config::get('variables.TWO');
                break;
            case Config::get('variables.OWNER'):
                $validatedUserData['role'] = Config::get('variables.THREE');
                break;
            case Config::get('variables.RIDER'):
                $validatedUserData['role'] = Config::get('variables.FOUR');
                break;
            case Config::get('variables.DRIVER'):
                $validatedUserData['role'] = Config::get('variables.FIVE');
                break;
            default:
                $validatedUserData['role'] = Config::get('variables.ONE');
                break;
        }
        $user = $this->userInterface->store('User', $validatedUserData);
        if (request()->expectsJson()) {
            return new AuthResource($user);
        }
    }

    public function login(Request $request)
    {
        $userData = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6'
        ]);
        if (Auth::attempt($userData)) {
            $user = User::find(auth()->user()->id);
            $token = $user->createToken('rideandsavor')->plainTextToken;
            return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }
        return response()->json([
            'message' => 'Invalid UserName And Password'
        ], 401);
    }

    public function logout(Request $request)
    {
        $user = User::find(auth()->user()->id);
        if (!$user) {
            return response()->json(['message' => 'No authenticated user'], 401);
        }
        $user->tokens->each(function ($token) {
            $token->delete();
        });
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
