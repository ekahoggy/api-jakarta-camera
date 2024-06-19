<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\ImageServiceProvider as ImageServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid as Generator;

# email
use App\Mail\VerifikasiEmail;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['checkEmail']]);
        $this->user = new User();
    }

    public function getData(Request $request){
        try {
            $data = $this->user->getUser($request);

            return response()->json([
                'data' => $data,
                'totalItems' => count($data),
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
                'status_code' => 500
            ], 500);
        }
    }

    public function getDetailUser($id){
        try {
            $data = $this->user->getDetailUser($id);

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

    public function create(Request $request) {
        $params = $request->all();

        // validasi
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
            if (!empty($params['photo'])) {
                $photo = ImageServiceProvider::uploadImage($params['photo'], 'images/users/photo');
            }

            $id = Generator::uuid4()->toString();
            $params['phone_number'] = ltrim($params['phone_number'], '0');
            $params['phone_number'] = ltrim($params['phone_number'], '+62');
            $params['phone_number'] = ltrim($params['phone_number'], '62');

            $user = User::create([
                'id' => $id,
                'type' => $request['type'] ? $request['type'] : 'customer' ,
                'username' => $request['username'],
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'phone_code' => $request['phone_code'],
                'phone_number' => $request['phone_number'],
                'address' => $request['address'],
                'photo' => $photo,
                'roles_id' => $request['roles_id'],
                'is_active' => $request['is_active'] ? $request['is_active'] : 'aktif',
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

    public function update(Request $request, $id){
        try {
            $getDataUser = $this->user->getDetailUser($id);

            $data = [
                'type' => $request->type ? $request->type : $getDataUser->type,
                'username' => $request->username ? $request->username : $getDataUser->username,
                'name' => $request->name ? $request->name : $getDataUser->name,
                'email' => $request->email ? $request->email : $getDataUser->email,
                'password' => $request->password ? Hash::make($request->password) : false,
                'phone_code' => $request->phone_code ? $request->phone_code : $getDataUser->phone_code,
                'phone_number' => $request->phone_number ? $request->package_number : $getDataUser->phone_number,
                'address' => $request->address ? $request->address : $getDataUser->address,
                'roles_id' => $request->roles_id ? $request->roles_id : $getDataUser->roles_id,
                'is_active' => $request->is_active ? $request->is_active : $getDataUser->is_active
            ];

            $data['phone_number'] = ltrim($data['phone_number'], '0');
            $data['phone_number'] = ltrim($data['phone_number'], '+62');
            $data['phone_number'] = ltrim($data['phone_number'], '62');
            if($data['password'] === false){
                unset($data['password']);
            }

            //upload image
            if (!empty($request->photo)) {
                $data['photo'] = ImageServiceProvider::uploadImage($request->photo, 'images/users/photo');
            }

            $user = $this->user->updateData($id, $data);

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

    public function checkEmail (){
        $data = [
            'subject' => 'Subjek',
            'message' => 'Ini adalah contoh email custom'
        ];

        Mail::to('sgalih1234@gmail.com')->send(new VerifikasiEmail($data));

        return response()->json([
            'message' => 'Notifikasi berhasil dikirim'
        ]);
    }
}
