<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric|min:10'
        ]);

        $user = User::firstOrCreate([
            'phone' => $request->phone
        ]);

        if (!$user)
            return response()->json(['message' => 'Could not process a user with that phone number'], 401);

        $user->notify(new LoginNeedsVerification());

        return response()->json(['message' => 'Text Message notification sent']);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'login_code' => 'required|numeric|between:111111,999999',
            'phone' => 'required|string|min:10'
        ]);

        $user = User::where([
            'phone' => $request->phone,
            'login_code' => $request->login_code
        ])->first();

        if ($user) {
            $user->update(['login_code' => null]);
            return $user->createToken($request->login_code)->plainTextToken;
        }
        return response()->json(['message' => "The login code doesn't match"], 401);
    }
}
