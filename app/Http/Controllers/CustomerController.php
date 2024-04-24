<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customer;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['customer']]);
        $this->customer = new Customer();
    }

    public function getData(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->customer->getAll($params);

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

    public function getDataById($id){
        try {
            $data = $this->customer->getById($id);

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

    public function simpan(Request $request){
        try {
            $params = (array) $request->only('id', 'type', 'username', 'name', 'email', 'password', 'phone_code', 'phone_number', 'remember_token', 'address', 'photo', 'roles_id', 'kode', 'email_expired', 'is_active',);
            $data = $this->customer->simpan($params);

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

    public function customer() {
        $customerModel = new Customer();
        $customer = $customerModel->getCustomer();

        if($customer){
            return response()->json(['status_code' => 200, 'data' => $customer], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }
}
