<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $role;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->role = new Role();
    }

    public function getData(Request $request){
        try {
            $data = $this->role->getUser($request);

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

    public function getDetailUser(Request $request, $id){
        try {
            $data = $this->role->getDetailUser($id);

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
