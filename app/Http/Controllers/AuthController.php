<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\Crypt;

// Email
use App\Mail\ResetKataSandi;
use App\Mail\VerifikasiEmail;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        # By default we are using here auth:api middleware
        $this->middleware('auth:api', ['except' => ['loginGoogle','login', 'register', 'me', 'verif', 'forgot', 'reset']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        $data = [
            'user' => $user,
            'auth' => $this->respondWithToken($token)
        ];
        return response()->json([
                'status_code' => 200,
                'data'  => $data,
            ], 200);
    }

    public function loginGoogle(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if(!$user)
        {
            $id = Generator::uuid4()->toString();
            $user = User::create([
                'id' => $id,
                'gauth_id' => $request->id,
                'type' => 'customer',
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make(rand(100000,999999)),
                'photo' => $request->avatar,
                'is_active' => 'aktif',
            ]);

            $user->id = $id;
            $userData = User::where("id", $user->id)->first();
            $token = Auth::login($userData);
            $u = Auth::user();
            $data = [
                'user' => $u,
                'auth' => $this->respondWithToken($token)
            ];
            return response()->json([
                'status_code' => 200,
                'data'  => $data,
            ], 200);
        }
        else{
            User::where("id", $user->id)->update([
                "name" => $request->name,
                "gauth_id" => $request->id,
                "gauth_type" => "google",
                "photo" => $request->photoUrl,
                "is_active" => "aktif"
            ]);

            $userData = User::where("id", $user->id)->first();
            $token = Auth::login($userData);
            $u = Auth::user();
            $data = [
                'user' => $u,
                'auth' => $this->respondWithToken($token)
            ];
            return response()->json([
                'status_code' => 200,
                'data'  => $data,
            ], 200);
        }
    }

    public function register(Request $request){
        $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:5',
            'phone_code' => 'required|string|max:3',
            'phone_number' => 'required|string|max:15|unique:users'
        ]);

        $id = Generator::uuid4()->toString();

        $phoneNumber = $request->phone_number;
        if (isset($phoneNumber)) {
            $payloadPhoneNumber = ltrim($phoneNumber, '0');
            $payloadPhoneNumber = ltrim($phoneNumber, '+62');
            $payloadPhoneNumber = ltrim($phoneNumber, '62');
        }

        $user = User::create([
            'id' => $id,
            'type' => 'customer',
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_code' => $request->phone_code,
            'phone_number' => $payloadPhoneNumber,
            'is_active' => 'verifikasi',
        ]);

        $appUrl = env("APP_URL", "http://localhost:8000");
        $data = [
            'id' => $id,
            'name' => $request->name,
            'email' => $request->email,
            'link_verif' => "$appUrl/api/v1/auth/verif-email?token=" . urlencode(Crypt::encrypt($id))
        ];

        Mail::to($request->email)->send(new VerifikasiEmail($data));

        $user->id = $id;
        $token = Auth::login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => $this->respondWithToken($token)
        ]);
    }

    public function verif(Request $request){
        $user = new User();
        $params = $request->only('token');
        $tokenVerif = urldecode($params['token']);
        $id = Crypt::decrypt($tokenVerif);

        $user->changeStatus($id, 'aktif');

        return redirect(env('APP_CLIENT_URL', 'https://jakartacamera.moodstudio.id/') . 'success-verification');
    }

    public function forgot(Request $request) {
        $user = new User();
        $params = $request->only('email');
        $users = $user->getByEmail($params['email']);

        if (empty($users)) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Email belum terdaftar!',
            ], 401);
        }

        $appUrl = env('APP_CLIENT_URL', 'http:localhost:4200/');
        $data = [
            'link_reset' => "{$appUrl}api/v1/auth/reset-password?token=" . urlencode(Crypt::encrypt($params['email']))
        ];

        Mail::to($params['email'])->send(new ResetKataSandi($data));

        return response()->json([
            'status_code' => 200,
            'message' => 'Sukses! Email untuk reset kata sandi telah berhasil dikirim. Silakan periksa kotak masuk Anda untuk instruksi lebih lanjut.',
        ], 200);
    }

    public function reset(Request $request) {
        $user = new User();
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $params = $request->only('password', 'password_confirmation', 'token');
        $tokenVerif = urldecode($params['token']);
        $email = Crypt::decrypt($tokenVerif);

        $payload = [
            'email' => $email,
            'password' => $params['password']
        ];

        $user->updatePasswordByEmail($payload);

        return response()->json([
            'status_code' => 200,
            'message' => 'Sukses! Email untuk reset kata sandi telah berhasil dikirim. Silakan periksa kotak masuk Anda untuk instruksi lebih lanjut.',
        ], 200);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        if(auth()->user()){
            return response()->json([
                'status_code' => 200,
                'data' => auth()->user()
            ], 200);
        }
        else{
            return response()->json([
                'status_code' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status_code' => 200,
            'message' => 'Successfully logged out',
        ], 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    /**
     * Check token is expired.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkToken()
    {
        return response()->json([
            'status_code' => 200,
            'message' => 'Token not expired yet'
        ], 200);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
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
