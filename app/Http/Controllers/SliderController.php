<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    protected $slider;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['slider']]);
        $this->slider = new Slider();
    }

    public function getData(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->slider->getAll($params);

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
            $data = $this->slider->getById($id);

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
        try {
            $params = (array) $request->all();
            $data = $this->slider->simpan($params);

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

    public function getDetail($id){
        try {
            $data = $this->slider->getDetail($id);

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

    public function slider() {
        $sliders = $this->slider->getSlider();

        if($sliders){
            return response()->json(['status_code' => 200, 'data' => $sliders], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function changeStatus(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->slider->changeStatus($params);

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

    public function moveSlider(Request $request) {
        $params = (array) $request->all();
        try {
            $payload['index_position'] = $params['curr'];

            $model = Slider::find($params['id']);
            if($model->index_position < $payload['index_position']){
                Slider::where('index_position', '>',$payload['index_position'])->increment('index_position', 1);
                Slider::where('index_position','>',$model->index_position)->decrement('index_position', 1);
            }else{
                Slider::where('id', '!=', $model->id)->where('index_position', '>=',$payload['index_position'])->increment('index_position', 1);
                Slider::where('id', '!=', $model->id)->where('index_position','>',$model->index_position)->decrement('index_position', 1);
            }

            $model->update($payload);
            return response()->json(['status_code' => 200, 'data' => true], 200);
        } catch (\Throwable $th) {
            return false;
        }

    }
}
