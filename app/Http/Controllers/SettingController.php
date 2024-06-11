<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected $setting;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getSetting']]);
        $this->setting = new Setting();
    }

    public function getSetting(Request $request) {
        $params = (array) $request->all();
        $setting = $this->setting->getAll(['kategori' => $params['kategori']]);

        if($setting){
            return response()->json(['status_code' => 200, 'data' => $setting], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function simpan(Request $request) {
        $params = (array) $request->all();

        $data = $this->setting->simpan($params);
        return response()->json(['status_code' => 200, 'data' => $data], 200);
    }

    public function updatePopUp(Request $request) {
        $params = (array) $request->all();

        $data = $this->setting->updateSettingPopup($params);
        return response()->json(['status_code' => 200, 'data' => $data], 200);
    }

    public function getDataById($id) {
    }
}
