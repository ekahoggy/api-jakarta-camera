<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\PromoDet;
use App\Models\VoucherDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    protected $produk;
    protected $promoDet;
    protected $voucherDet;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['produk']]);
        $this->produk = new Produk();
        $this->promoDet = new PromoDet();
        $this->voucherDet = new VoucherDetail();
    }

    public function getData(Request $request){
        $produkModel = new Produk();
        $params = (array) $request->all();

        $produk = $produkModel->getAll($params);
        foreach ($produk['list'] as $key => $value) {
            $value->variant = $produkModel->getVariant($value->id);
            $value->photo_product = $produkModel->getPhoto($value->id);
            $value->foto = $produkModel->getMainPhotoProduk($value->id);
            $value->video = $produkModel->getVideo($value->id);
            $value->rowspan = count($value->variant);
        }
        return response()->json(['success' => true, "data" => $produk]);
    }

    public function getProdukById(Request $request, $id){
        $data = Produk::where('is_active', 1)->where('id', $id)->first();

        return response()->json(['success' => true, "data" => $data]);
    }

    public function getProdukByKategori(Request $request){
        $params = (array) $request->all();
        $produkModel = new Produk();
        $promo = $this->promoDet->getDetailPromoAktif();
        $voucher = $this->voucherDet->getDetailVoucherAktif();
        $data = $produkModel->getProdukKategori($params);

        return response()->json(['success' => true, "data" => $data, "promo" => $promo, "voucher" => $voucher]);
    }

    public function getProdukByBrand(Request $request){
        $params = (array) $request->all();
        $produkModel = new Produk();
        $promo = $this->promoDet->getDetailPromoAktif();
        $voucher = $this->voucherDet->getDetailVoucherAktif();
        $data = $produkModel->getProdukBrand($params);

        return response()->json(['success' => true, "data" => $data, "promo" => $promo, "voucher" => $voucher]);
    }

    public function produk() {
        $produkModel = new Produk();
        $produk = $produkModel->getAll();

        if ($produk){
            return response()->json(['status_code' => 200, 'data' => $produk], 200);
        } else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function getProdukSlug(Request $request) {
        $produkModel = new Produk();
        $produk = $produkModel->getBySlug($request->slug);
        if($produk){
            return response()->json(['status_code' => 200, 'data' => $produk], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function simpan(Request $request){
        $validator = Validator::make($request->all(), [
            "sku"    => "required|string|min:3",
            "nama"    => "required|string|min:3",
            "photo"    => "required|array"
        ]);

        if ($validator->valid()) {
            $params = (array) $request->only(
                "id", "nama", "sku", "photo", "slug", "deskripsi", "detail_produk", "harga", "in_box", "is_active",
                "m_brand_id", "m_kategori_id", "min_beli", "lebar", "berat", "panjang", "tinggi", "stok", "tags", "type", "variant",
                "link_blibli", "link_bukalapak", "link_lazada", "link_shopee", "link_tokped", "video", "link_video",
                "created_at", "created_by", "updated_at", "updated_by"
            );

            $data = $this->produk->simpan($params);

            return response()->json([
                'data' => $data,
                'status_code' => 200
            ], 200);
        }
        else{
            return response()->json([
                'message' => $validator,
                'status_code' => 500
            ], 500);
        }
    }

    public function getPhoto($id) {
        $produkModel = new Produk();
        $photo = $produkModel->getPhoto($id);

        if($photo){
            return response()->json(['status_code' => 200, 'data' => $photo], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function ubahStatus(Request $request) {
        $params = (array) $request->all();
        $produkModel = new Produk();
        $data = [
            'id' => $params['id'],
            'is_active' => $params['is_active']
        ];
        $res = $produkModel->ubahStatus($data);

        if($res){
            return response()->json(['status_code' => 200, 'data' => $res], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function getVariant($id) {
        $produkModel = new Produk();
        $variant = $produkModel->getVariant($id);

        $groupedData = [];
        $varian1 = '';
        $varian2 = '';
        foreach ($variant['all_varian'] as $item) {
            $item->image = Storage::url('images/produk-variant/' . $item->image);
            $varian1 = $item->varian1_type;
            $varian2 = $item->varian2_type;
            $var = $item->varian1;
            if (!isset($groupedData[$var])) {
                $groupedData[$var] = [];
            }
            $groupedData[$var][] = $item;
        }
        if($variant['all_varian']){
            return response()->json(['status_code' => 200,
            'data' => $variant['all_varian'],
            'group' => array_values($groupedData),
            'varian1' => $varian1,
            'varian2' => $varian2
        ], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function updateStok(Request $request){
        $params = (array) $request->all();
        $produkModel = new Produk();
        $data = $produkModel->updateStok($params);

        if($data){
            return response()->json(['status_code' => 200, 'data' => $data], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function updateStokProduk(Request $request){
        $params = (array) $request->all();
        $produkModel = new Produk();
        $data = $produkModel->updateStokProduk($params);

        if($data){
            return response()->json(['status_code' => 200, 'data' => $data], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function varian($type){
        $produkModel = new Produk();
        $variant = $produkModel->varian($type);

        if($variant){
            return response()->json(['status_code' => 200, 'data' => $variant], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function prosesVariant(Request $request){
        $params = (array) $request->all();
        $data_utama = (array) $params['data_utama'];
        $data_second = (array) $params['data_second'];
        // Calculate the number of duplicates needed
        $num_duplicates = count($data_second);
        // Create an array to hold the duplicated data
        $duplicated_data = [];
        // Duplicate data1 based on the number of items in data2
        for ($i = 0; $i < $num_duplicates; $i++) {
            foreach ($data_utama as $item) {
                $duplicated_data[] = $item;
            }
        }
        // Merge the duplicated data back into data1
        $hasilMerge = array_merge($data_utama, $duplicated_data);
        $splitDataUtama = [];
        foreach ($hasilMerge as $key => $value) {
            $value['image'] = '';
            $splitDataUtama[$value['varian1']][] = $value;
        }

        foreach ($splitDataUtama as $key => $value) {
            foreach ($data_second as $i => $s) {
                foreach ($value as $k => $v) {
                    if($i === $k){
                        $splitDataUtama[$key][$k]['varian2'] = $s;
                    }
                }
            }
        }
        $data = [];
        foreach ($splitDataUtama as $key => $value) {
            foreach ($value as $k => $v) {
                $data[] = $v;
            }
        }

        $hasil = $this->removeDuplicates(array_values($data));
        return response()->json(['status_code' => 200, 'data' => array_values($hasil)], 200);
    }

    function removeDuplicates($data) {
        $uniqueData = [];
        $seen = [];

        foreach ($data as $item) {
            $key = $item['varian1'] . '-' . $item['varian2'];
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $uniqueData[] = $item;
            }
        }

        return $uniqueData;
    }

    function prosesPhotoVarian(Request $request){
        $params = (array) $request->all();
        $data = $this->mergeImages($params['data1'], $params['data2']);
        return response()->json(['status_code' => 200, 'data' => $data], 200);
    }

    function mergeImages(&$data1, &$data2) {
        // Iterate through each item in data2
        foreach ($data2 as &$item2) {
            // Find the corresponding item in data1 based on varian1
            foreach ($data1 as $item1) {
                if ($item1['varian1'] === $item2['varian1']) {
                    // If a match is found, copy the image
                    $item2['image'] = $item1['image'];
                    break;
                }
            }
        }
    }
}
