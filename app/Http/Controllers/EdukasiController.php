<?php

namespace App\Http\Controllers;

use App\Models\Edukasi;
use App\Models\EdukasiOrder;
use App\Models\Xendit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EdukasiController extends Controller
{
    protected $edukasi;
    protected $order;
    protected $xendit;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['edukasi', 'pay', 'getDataBySlug']]);
        $this->edukasi = new Edukasi();
        $this->order = new EdukasiOrder();
        $this->xendit = new Xendit();
    }

    public function getData(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->edukasi->getAll($params);

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
            $data = $this->edukasi->getById($id);

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
        $validator = Validator::make($params['data'], [
            "judul"  => "required"
        ]);
        if ($validator->valid()) {
            $data = $this->edukasi->simpan($params);

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

    public function changeStatus(Request $request){
        $params = (array) $request->all();
        $data = $this->edukasi->changeStatus($params);

        return response()->json([
            'data' => $data,
            'status_code' => 200
        ], 200);
    }

    public function pay(Request $request){
        $params = (array) $request->all();
        $data = $this->order->createOrder($params);

        return response()->json([
            'data' => $data,
            'status_code' => 200
        ], 200);
    }

    public function getDetail($id){
        try {
            $data = $this->edukasi->getDetail($id);

            foreach ($data as $key => $value) {
                $data[$key]->video_url = Storage::url('videos/edukasi/' . $value->video_url);
            }
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

    public function getDataBySlug($slug){
        try {
            $data = $this->edukasi->getBySlug($slug);
            $detail = $this->edukasi->getDetail($data->id);
            $data->url_gambar = Storage::url('images/edukasi/' . $data->gambar);
            $data->mainVideo = '';
            $data->lockMainVideo = true;

            foreach ($detail as $key => $value) {
                $value->url_video = Storage::url('videos/edukasi/' . $value->video_url);
                if($value->urutan === 1 && $value->is_lock === 0){
                    $data->mainVideo = Storage::url('videos/edukasi/' . $value->video_url);
                    $data->lockMainVideo = false;
                }
            }

            return response()->json([
                'data' => $data,
                'detail' => $detail,
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
                'status_code' => 500
            ], 500);
        }
    }

    public function edukasi(Request $request) {
        $params = $request->only('filter');
        $categories = $this->edukasi->getEdukasi($params);

        if($categories){
            return response()->json(['status_code' => 200, 'data' => $categories], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }
}
