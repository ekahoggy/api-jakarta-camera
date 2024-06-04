<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid as Generator;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }


    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::where('email', $googleUser->email)->first();
        if(!$user)
        {
            $id = Generator::uuid4()->toString();
            $user = User::create([
                'id' => $id,
                'gauth_id' => $googleUser->id,
                'type' => 'customer',
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => Hash::make(rand(100000,999999)),
                'photo' => $googleUser->avatar,
                'is_active' => 'aktif',
            ]);

            $user->id = $id;
            $token = Auth::login($user);
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => $user,
                'authorisation' => $this->respondWithToken($token)
            ]);
        }
    }

    protected function respondWithToken($token)
    {
        # This function is used to make JSON response with new
        # access token of current user
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 160
        ]);
    }
}
