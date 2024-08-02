<?php

namespace App\Http\Controllers;

use App\Mail\PasangCctv;
use Illuminate\Http\Request;
use App\Models\JasaPasangCctv;
use App\Models\Service;

use Illuminate\Support\Facades\Mail;

class JasaPasangCctvController extends Controller
{
    protected $jasaPasang;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['simpan']]);
        $this->jasaPasang = new JasaPasangCctv();
    }

    public function getData(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->jasaPasang->getAll($params);

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
            $data = $this->jasaPasang->getById($id);

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
            'nama' => 'required|string',
            'email' => 'required|email',
            'phone_code' => 'required',
            'phone_number' => 'required|numeric',
            'pesan' => 'required|string|min:50',
            'province_id' => 'required', 
            'subdistrict_id' => 'required', 
            'city_id' => 'required', 
            'village_id' => 'required', 
            'postal_code' => 'required', 
            'address' => 'required', 
            'latitude' => 'required', 
            'longitude' => 'required',
        ]);

        try {
            // $service = new Service();
            $params = (array) $request->all('nama', 'email', 'phone_code', 'phone_number', 'pesan', 'province_id', 'subdistrict_id', 'city_id', 'village_id', 'postal_code', 'address', 'latitude', 'longitude');

            // $params['file'] = $service->saveImage("servis-kamera/", $params['file']);
            $data = JasaPasangCctv::create($params);

            $data = [
                'nama' => $request->nama,
                'email' => $request->email,
            ];    
            Mail::to($request->email)->send(new PasangCctv($data));

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
