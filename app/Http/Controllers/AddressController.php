<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    protected $address;

    public function __construct()
    {
        $this->address = new Address();
    }

    public function validasi($request) {
        $validator = Validator::make($request->all(), [
            "phone_code" => "required",
            "recipient" => "required",
            "phone_number" => "required",
            "province_id" => "required",
            "city_id" => "required",
            "subdistrict_id" => "required",
            "village_id" => "required",
            "postal_code" => "required",
            "address" => "required",
        ]);
    
        return $validator;
    }

    public function getData(Request $request) {
        $params = $request->all();
        $data = $this->address->getAll($params);

        return response()->json([
            'data' => $data,
            'status_code' => 200, 
            'message' => 'Successfully show list address'
        ], 200);
    }

    public function getAddress(Request $request) {
        $params = $request->all();
        $data = $this->address->getAddress($params);

        return response()->json([
            'data' => $data,
            'status_code' => 200, 
            'message' => 'Successfully show list address'
        ], 200);
    }

    public function getAddressById(Request $request) {
        $params = $request->all();
        $data = $this->address->getAddressById($params['id']);

        return response()->json([
            'data' => $data,
            'status_code' => 200, 
            'message' => 'Successfully show list address'
        ], 200);
    }

    public function saveAddress(Request $request) {
        $params = $request->all();
        $validator = $this->validasi($request);
    
        // Periksa jika validasi gagal
        if ($validator->fails()) {
            return response()->json(['status_code' => 422, 'message' => $validator->errors()], 422);
        }

        if (isset($params['id']) && !empty($params['id'])) {
            $model = $this->address->updateAddress($params);
        } else {
            $model = $this->address->insertAddress($params);
        }

        if ($model === true || ($model === 1 || $model == 0)) {
            return response()->json(['status_code' => 200, 'message' => 'Successfully save address'], 200);
        }

        return response()->json(['status_code' => 422, 'message' => 'An error occurred on the server'], 422);
    }

    public function updateAddress(Request $request) {
        $params = $request->all();
        $model = $this->address->changeAddress($params);

        if ($model === true || $model === 1) {
            return response()->json(['status_code' => 200, 'message' => 'Successfully change quantity'], 200);
        }

        return response()->json(['status_code' => 422, 'message' => 'An error occurred on the server'], 422);
    }

    public function deleteAddress(Request $request) {
        $params = $request->all();
        $model = $this->address->deleteAddress($params);

        if ($model === true || $model === 1) {
            return response()->json(['status_code' => 200, 'message' => 'Successfully delete address'], 200);
        }

        return response()->json(['status_code' => 422, 'message' => 'An error occurred on the server'], 422);
    }
}
