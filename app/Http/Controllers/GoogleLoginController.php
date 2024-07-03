<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Http\RedirectResponse;

class GoogleLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['redirectToGoogle', 'handleGoogleCallback']]);
    }

    public function redirectToGoogle(): RedirectResponse
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
        else{
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
