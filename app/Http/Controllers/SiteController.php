<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


use App\Models\Slider;
use App\Models\Produk;

class SiteController extends Controller
{
    protected $slider;
    protected $product;

    public function __construct()
    {
        $this->slider = new Slider();
        $this->product = new Produk();
    }

    public function slider() {
        $slider = $this->slider->getAll([]);

        if($slider){
            return response()->json(['status_code' => 200, 'data' => $slider], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function getProduct(Request $request){
        $params = (array) $request->all();

        $produk = $this->product->getAll($params);
        foreach ($produk['list'] as $key => $value) {
            $value->variant = $this->product->getVariant($value->id);
            $value->photo_product = $this->product->getPhoto($value->id);
            $value->foto = Storage::url('images/produk/' . $value->media_link);
            $value->rowspan = count($value->variant);
        }
        return response()->json(['success' => true, "data" => $produk]);
    }

    public function getProdukSlug(Request $request) {
        $produk = $this->product->getBySlug($request->slug);
        $produk->variant = $this->product->getVariant($produk->id);
        $produk->foto = Storage::url('images/produk/' . $produk->media_link);

        foreach ($produk->detail_foto as $value) {
            $value->foto = Storage::url('images/produk/' . $value->media_link);
        }

        if($produk){
            return response()->json(['status_code' => 200, 'data' => $produk], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }
}
