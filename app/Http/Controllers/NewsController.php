<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    protected $news;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['news']]);
        $this->news = new News();
    }

    public function getData(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->news->getAll($params);

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
            $data = $this->news->getById($id);

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

    public function changeStatus(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->news->changeStatus($params);

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
        $params = (array) $request->all();
        $validator = Validator::make($params, [
            "judul"  => "required",
            "image"  => "required",
            "tags"  => "required|array"
        ]);
        if ($validator->valid()) {
            $data = $this->news->simpan($params);

            return response()->json([
                'data' => $data,
                'status_code' => 200
            ], 200);
        }
        else{
            return response()->json([
                'message' => $validator->errors(),
                'status_code' => 500
            ], 500);
        }
    }

    public function news() {
        $news = $this->news->getNews();

        if($news){
            return response()->json(['status_code' => 200, 'data' => $news], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }
}
