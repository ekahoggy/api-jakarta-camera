<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    protected $order;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['laporanPenjualan']]);
        $this->order = new Order();
    }

    public function laporanPenjualan(Request $request) {
        $params = (array) $request->all();

        $data = $this->order->generateLaporan($params);
        $newArr = [];
        $total = 0;
        foreach ($data as $key => $value) {
            $newArr[$value->invoice_number]['invoice'] = $value->invoice_number;
            $newArr[$value->invoice_number]['rowspan'] = 0;
            $newArr[$value->invoice_number]['detail'][] = $value;

            $total += $value->grand_total;
        }

        foreach ($newArr as $key => $value) {
            $newArr[$key]['rowspan'] = count($value['detail']);
        }

        return response()->json([
            'data' => array_values($newArr),
            'total' => $total,
            'status_code' => 200
        ], 200);
    }
}
