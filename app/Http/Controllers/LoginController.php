<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        //validate the phine number
        $request->validate([
            'phone' => 'required|numeric|min:10'
        ]);

        //find or create user model
        $user = User::firstOrCreate([
            'phone' => $request->phone,
            // 'login_code' => '888888'
        ]);

        if (!$user) {
            return response()->json(['message' => 'Could not process user with the phone number'], 401);
        }

        //send the user a one time use code
        $user->notify(new LoginNeedsVerification());

        return response()->json(['message' => 'Text message notification sent!'], 200);

    }

    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric|min:10',
            'login_code' => 'required|numeric|between:111111,999999',
        ]);

        $user = User::where([
            'phone' => $request->phone,
            'login_code' => $request->login_code
        ])->first();

        if ($user) {
            $user->update([
                'login_code' => null
            ]);
            $token = $user->createToken($request->login_code);

            return ['token' => $token->plainTextToken];
        }

        return response()->json(['message' => 'invalid verification code!'], 401);

    }
}
