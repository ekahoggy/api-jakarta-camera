<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JasaServis;
use App\Models\Service;

use App\Mail\ServisKamera;
use Illuminate\Support\Facades\Mail;

class JasaServisController extends Controller
{
    protected $jasaServis;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['simpan']]);
        $this->jasaServis = new JasaServis();
    }

    public function getData(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->jasaServis->getAll($params);

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
            $data = $this->jasaServis->getById($id);

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
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone_code' => 'required',
            'phone_number' => 'required|numeric',
            'keterangan' => 'required|string|min:50',
        ]);

        try {
            $service = new Service();
            $params = (array) $request->all('name', 'email', 'phone_code', 'phone_number', 'keterangan', 'file');

            $params['file'] = $service->saveImage("servis-kamera/", $params['file']);
            $data = JasaServis::create($params);

            $data = [
                'name' => $request->name,
                'email' => $request->email,
            ];    
            Mail::to($request->email)->send(new ServisKamera($data));

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

    public function update(Request $request){
        try {
            $service = new Service();
            $params = (array) $request->all('name', 'email', 'phone_code', 'phone_number', 'keterangan', 'file');
            
            $params['file'] = $service->saveImage("promo-slider/", $params['file']);
            $data = JasaServis::create($params);

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
