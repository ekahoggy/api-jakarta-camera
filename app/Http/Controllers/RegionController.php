<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    protected $region;

    public function __construct()
    {
        $this->region = new Region();
    }

    public function village(Request $request) {
        $params = $request->all();
        $data = $this->region->village($params);

        return response()->json([
            'data' => $data,
            'status_code' => 200,
            'message' => 'Successfully show list region'
        ], 200);
    }

    public function subdistrict(Request $request) {
        $params = $request->all();
        $data = $this->region->subdistrict($params);

        return response()->json([
            'data' => $data,
            'status_code' => 200,
            'message' => 'Successfully show list region'
        ], 200);
    }

    public function city(Request $request) {
        $params = $request->all();
        $data = $this->region->city($params);

        return response()->json([
            'data' => $data,
            'status_code' => 200,
            'message' => 'Successfully show list region'
        ], 200);
    }

    public function province(Request $request) {
        $data = $this->region->province();

        return response()->json([
            'data' => $data,
            'status_code' => 200,
            'message' => 'Successfully show list region'
        ], 200);
    }
}
