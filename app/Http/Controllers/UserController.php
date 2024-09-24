<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\TaxiDriver;
use Illuminate\Http\Request;
use App\Contracts\UserInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class UserController extends Controller
{
    public $userInterface;
    public function __construct(UserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }

    public function changeRoleToDriver()
    {
        $user = Auth::user(); // Get authenticated user 
        // dd($user['id']);
        if ($user['role'] === Config::get('variable.DRIVER_ROLE_NO')) {
            return response()->json([
                'message' => 'User is already a driver',
            ], 400);
        }

        // Update the user's role
        User::where('id', $user['id'])->update(['role' => Config::get('variable.DRIVER_ROLE_NO')]);

        // Create a new taxi driver record
        $taxiDriver = TaxiDriver::create([ 
            'user_id' => $user['id'], // Assuming a relationship between User and TaxiDriver
            'current_location' => null,
            'is_available' => true,
        ]);

        return response()->json([
            'message' => 'Role updated to driver and taxi driver information created',
            'taxi_driver' => $taxiDriver,
        ]);
    }

}
