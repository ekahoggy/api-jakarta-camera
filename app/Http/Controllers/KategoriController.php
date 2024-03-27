<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    protected $kategori;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->kategori = new Kategori();
    }

    public function getData(Request $request){
        try {
            $data = $this->kategori->getKategori($request);

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
            $data = $this->kategori->getDetailUser($id);

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
