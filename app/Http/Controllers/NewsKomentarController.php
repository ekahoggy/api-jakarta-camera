<?php

namespace App\Http\Controllers;

use App\Models\NewsKomentar;
use Illuminate\Http\Request;

class NewsKomentarController extends Controller
{
    protected $komentar;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getKomentar']]);
        $this->komentar = new NewsKomentar();
    }

    public function getData(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->komentar->getAll($params);

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

    public function post(Request $request) {
        try {
            $params = (array) $request->only('news_id', 'nama', 'email', 'komentar', 'user_id');
            $data = $this->komentar->postKomentar($params);

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

    public function changeStatus(Request $request) {
        try {
            $params = (array) $request->all();
            $data = $this->komentar->changeStatus($params);

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

    public function postKomentar(Request $request) {
        try {
            $params = (array) $request->all();
            $data = $this->komentar->postBalasan($params);

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

    public function getKomentar() {
        $komentar = $this->komentar->getNewsKomentar();

        if($komentar){
            return response()->json(['status_code' => 200, 'data' => $komentar], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }
}
