<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\ImageServiceProvider as ImageServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid as Generator;

class UserController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->user = new User();
    }

    public function getData(Request $request){
        try {
            $data = $this->user->getUser($request);

            return response()->json([
                'data' => $data,
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
                'status_code' => 500
            ], 500);
        }
    }

    public function create(Request $request){
        $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:5',
            'phone_code' => 'required|string|max:3',
            'phone_number' => 'required|string|max:15',
            'roles_id' => 'required|string'
        ]);

        try {
            //upload image
            $photo = '';
            if (!empty($request->photo)) {
                $photo = ImageServiceProvider::uploadImage($request->photo, 'images/users/photo');
            }

            $id = Generator::uuid4()->toString();
            $request->phone_number = ltrim($request->phone_number, '0');
            $request->phone_number = ltrim($request->phone_number, '+62');
            $request->phone_number = ltrim($request->phone_number, '62');

            $user = User::create([
                'id' => $id,
                'type' => $request->type ? $request->type : 'customer' ,
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_code' => $request->phone_code,
                'phone_number' => $request->phone_number,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'photo' => $photo,
                'roles_id' => $request->roles_id,
                'is_active' => $request->is_active ? $request->is_active : 'aktif',
            ]);
            $user->id = $id;

            return response()->json([
                'data' => $user,
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
                'status_code' => 500
            ], 500);
        }
    }

    public function changeStatus(Request $request){
        $request->validate([
            'id' => 'required',
            'is_active' => 'required'
        ]);

        try {
            $data = $this->user->changeStatus($request->id, $request->is_active);

            return response()->json([
                'data' => $data,
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
                'status_code' => 500
            ], 500);
        }
    }
}