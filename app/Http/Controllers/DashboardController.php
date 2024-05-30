<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $order;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
        $this->order = new Order();
    }

    public function pendapatan(Request $request){
        try {
            $params = (array) $request->all();
            $params['bulan'] = date('m');
            $data = $this->order->getAll($params);
            $totalPendapatan = 0;
            $persen = 0;
            foreach ($data['list'] as $key => $value) {
                if($value->status_order === 'received'){
                    $totalPendapatan += $value->grand_total;
                }
            }
            return response()->json([
                'data' => [
                    'total_pendapatan' => $totalPendapatan,
                    'persen' => $persen
                ],
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
                'status_code' => 500
            ], 500);
        }
    }

    public function penjualanhariini(Request $request){
        try {
            $params = (array) $request->all();
            $params['hari'] = date('d');
            $data = $this->order->getAll($params);
            $totalPendapatan = 0;
            $persen = 0;
            foreach ($data['list'] as $key => $value) {
                if($value->status_order === 'received'){
                    $totalPendapatan += $value->grand_total;
                }
            }
            return response()->json([
                'data' => [
                    'total_pendapatan' => $totalPendapatan,
                    'persen' => $persen
                ],
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
