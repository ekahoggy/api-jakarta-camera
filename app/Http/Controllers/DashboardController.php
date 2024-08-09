<?php

namespace App\Http\Controllers;

use App\Models\LogUser;
use App\Models\Order;
use App\Models\Produk;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $order;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['counterPesanan']]);
        $this->order = new Order();
    }

    public function counterPesanan(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->order->getCounted();
            return response()->json([
                'data' => $data[0],
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
                'status_code' => 500
            ], 500);
        }
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

    function penjualanpertahun(Request $request) {
        try {
            $params = (array) $request->all();
            $params['tahun'] = date('Y');
            $data = $this->order->getAll($params);
            $totalPendapatan = 0;

            $months = [];
            $salesByMonth = [];
            for ($i = 1; $i <= 12; $i++) {
                $months[] = substr(date('F', strtotime($params['tahun'] . '-' . $i . '-01')), 0, 3);
                $salesByMonth[$i] = 0;
            }

            foreach ($data['list'] as $key => $value) {
                if ($value->status_order === 'received') {
                    $month = date('n', strtotime($value->date));
                    $salesByMonth[$month] += $value->grand_total;
                    $totalPendapatan += $value->grand_total;
                }
            }
            ksort($salesByMonth);

            return response()->json([
                'data' => [
                    'months' => $months,
                    'sales' => array_values($salesByMonth)
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

    function logUser(Request $request) {
        try {
            $params = (array) $request->all();
            $logUser = new LogUser();
            $data = $logUser->getAll($params);
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

    function reminderStok(Request $request) {
        try {
            $params = (array) $request->all();
            $produk = new Produk();
            $data = $produk->reminderStok();
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
