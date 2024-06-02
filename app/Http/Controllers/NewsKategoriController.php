<?php

namespace App\Http\Controllers;

use App\Models\NewsKategori;
use Illuminate\Http\Request;

class NewsKategoriController extends Controller
{
    protected $kategori;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['kategori']]);
        $this->kategori = new NewsKategori();
    }

    public function getData(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->kategori->getAll($params);

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

    public function save(Request $request) {
        $params = (array) $request->all();
    }

    public function kategori() {
        $kategori = $this->kategori->getNewsKategori();

        if($kategori){
            return response()->json(['status_code' => 200, 'data' => $kategori], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }
}
