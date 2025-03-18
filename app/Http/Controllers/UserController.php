<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\TaxiDriver;
use Illuminate\Http\Request;
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

    public function validateUsers(Request $request)
    {
        $validated = $request->validate([
            'userIds' => 'required|array|min:1',
            'userIds.*' => 'integer',
        ]);

        // Only include valid IDs that exist in the users table
        $validIds = User::whereIn('id', $validated['userIds'])->pluck('id')->toArray();

        return response()->json([
            'message' => count($validIds) > 0 ? 'Here are your valid users' : 'No valid user IDs found.',
            'validIds' => $validIds,
        ], 200);
    }


}
