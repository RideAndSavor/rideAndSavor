<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\TaxiDriver;
use App\Contracts\UserInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\UserRoleInfoRequest;

class UserController extends Controller
{
    public $userInterface;
    public function __construct(UserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }

    public function changeUserRole(UserRoleInfoRequest $request)
    { 
        // Update the user's role
        User::where('id', $request->user_id)->update(['role' => $request->role_id]);
         
        if ($request->role_id === Config::get('variable.DRIVER_ROLE_NO')) {
            // Create a new taxi driver record
            $taxiDriver = TaxiDriver::create([ 
                'user_id' => $request->user_id, // Assuming a relationship between User and TaxiDriver
                'current_location' => null,
                'is_available' => true,
            ]);
        }

        return response()->json([
            'message' => 'User Role has been updated successfully!',
            'taxi_driver' => $taxiDriver,
        ]);
    }

}
