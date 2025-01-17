<?php

namespace App\Http\Controllers;

use App\Models\BiteShip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\Customer;
use App\Models\Slider;
use App\Models\Produk;
use App\Models\Brand;
use App\Models\News;
use App\Models\Order;
use App\Models\Promo;
use App\Models\PromoDet;
use App\Models\NewsKategori;
use App\Models\NewsKomentar;
use App\Models\ProdukUlasan;
use App\Models\Subscription;
use App\Models\Voucher;

class SiteController extends Controller
{
    protected $user;
    protected $news;
    protected $customer;
    protected $slider;
    protected $product;
    protected $brand;
    protected $order;
    protected $promo;
    protected $promoDet;
    protected $newsCategory;
    protected $newsKomentar;
    protected $produkUlasan;
    protected $biteship;
    protected $subscribe;
    protected $voucher;
    protected $ulasan;

    public function __construct()
    {
        $this->user = new User();
        $this->customer = new Customer();
        $this->slider = new Slider();
        $this->product = new Produk();
        $this->brand = new Brand();
        $this->order = new Order();
        $this->promo = new Promo();
        $this->voucher = new Voucher();
        $this->promoDet = new PromoDet();
        $this->news = new News();
        $this->produkUlasan = new ProdukUlasan();
        $this->newsCategory = new NewsKategori();
        $this->newsKomentar = new NewsKomentar();
        $this->biteship = new BiteShip();
        $this->subscribe = new Subscription();
        $this->ulasan = new ProdukUlasan();
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

        $produk = $this->product->getAllPaginate($params);
        $promo = $this->promoDet->getDetailPromoAktif();

        foreach ($produk['list'] as $key => $value) {
            $value->variant = $this->product->getVariant($value->id);
            $value->foto = $this->product->getMainPhotoProduk($value->id);
            $value->photo_product = $this->product->getPhoto($value->id);
            $value->rating = $this->produkUlasan->getUlasanByProdukId($value->id)['rataRating'];
            $value->total_terjual = $this->order->getTotalTerjual($value->id)['total_terjual'];

            $value->is_promo = false;
            $value->is_flashsale = false;
            $value->promo = [];
            $value->harga_promo = $value->harga;
            foreach ($promo as $k => $p) {
                if($p->m_produk_id === $value->id){
                    $value->is_promo = true;
                    $hitungPromo = ($p->persen / 100) * $value->harga;
                    $value->harga_promo = $value->harga - $hitungPromo;
                    $value->promo = [
                        'm_promo_id' => $p->m_promo_id,
                        'kode' => $p->kode,
                        'promo' => $p->promo,
                        'tanggal_mulai' => $p->tanggal_mulai,
                        'jam_mulai' => $p->jam_mulai,
                        'tanggal_selesai' => $p->tanggal_selesai,
                        'jam_selesai' => $p->jam_selesai,
                        'persen' => $p->persen,
                        'nominal' => $p->nominal,
                        'qty' => $p->qty,
                        'promo_min_beli' => $p->promo_min_beli
                    ];
                }
            }
        }
        
        return response()->json(['success' => true, "data" => $produk]);
    }

    public function getProductPromo(Request $request){
        $params = (array) $request->all();

        $promo = $this->promoDet->getDetailPromoAktif();
        $produk = $this->product->getAll($params);

        foreach ($promo as $k => $p) {
            foreach ($produk['list'] as $key => $value) {
                if($p->m_produk_id === $value->id){
                    $p->variant = $this->product->getVariant($value->id);
                    $p->foto = $this->product->getMainPhotoProduk($value->id);
                    $p->photo_product = $this->product->getPhoto($value->id);
                    $p->rating = $this->produkUlasan->getUlasanByProdukId($value->id)['rataRating'];
                    $p->total_terjual = $this->order->getTotalTerjual($value->id)['total_terjual'];

                    $p->slug = $value->slug;
                    $p->harga_promo = $value->harga;
                    $p->kategori = $value->kategori;
                    $p->is_promo = true;
                    $hitungPromo = ($p->persen / 100) * $value->harga;
                    $p->harga_promo = $value->harga - $hitungPromo;
                    $p->promo = [
                        'm_promo_id' => $p->m_promo_id,
                        'kode' => $p->kode,
                        'promo' => $p->promo,
                        'tanggal_mulai' => $p->tanggal_mulai,
                        'jam_mulai' => $p->jam_mulai,
                        'tanggal_selesai' => $p->tanggal_selesai,
                        'jam_selesai' => $p->jam_selesai,
                        'persen' => $p->persen,
                        'nominal' => $p->nominal,
                        'qty' => $p->qty,
                        'promo_min_beli' => $p->promo_min_beli
                    ];
                }
            }
        }

        return response()->json(['success' => true, "data" => $promo]);
    }

    public function getFlashsale(Request $request){
        $params = (array) $request->all();

        $promo = $this->promoDet->getFlashsale();
        $produk = $this->product->getAll($params);

        foreach ($promo as $k => $p) {
            foreach ($produk['list'] as $key => $value) {
                if($p->m_produk_id === $value->id){
                    $p->variant = $this->product->getVariant($value->id);
                    $p->foto = $this->product->getMainPhotoProduk($value->id);
                    $p->photo_product = $this->product->getPhoto($value->id);

                    $p->slug = $value->slug;
                    $p->harga_promo = $value->harga;
                    $p->is_promo = true;
                    $hitungPromo = ($p->persen / 100) * $value->harga;
                    $p->harga_promo = $value->harga - $hitungPromo;
                    $p->promo = [
                        'm_promo_id' => $p->m_promo_id,
                        'kode' => $p->kode,
                        'promo' => $p->promo,
                        'tanggal_mulai' => $p->tanggal_mulai,
                        'jam_mulai' => $p->jam_mulai,
                        'tanggal_selesai' => $p->tanggal_selesai,
                        'jam_selesai' => $p->jam_selesai,
                        'persen' => $p->persen,
                        'nominal' => $p->nominal,
                        'qty' => $p->qty,
                        'promo_min_beli' => $p->promo_min_beli
                    ];
                }
            }
        }

        return response()->json(['success' => true, "data" => $promo]);
    }

    public function getLastSeenProduk(Request $request) {
        $params = (array) $request->all();
        if(!empty($params['lastseen'])){

            $promo = $this->promoDet->getDetailPromoAktif();
            $produk = $this->product->getAll($params);

            foreach ($produk['list'] as $key => $value) {
                $value->variant = $this->product->getVariant($value->id);
                $value->foto = $this->product->getMainPhotoProduk($value->id);
                $value->photo_product = $this->product->getPhoto($value->id);
                $value->rating = $this->produkUlasan->getUlasanByProdukId($value->id)['rataRating'];
                $value->total_terjual = $this->order->getTotalTerjual($value->id)['total_terjual'];

                $value->is_promo = false;
                $value->is_flashsale = false;
                $value->promo = [];
                $value->harga_promo = $value->harga;
                foreach ($promo as $k => $p) {
                    if($p->m_produk_id === $value->id){
                        $value->is_promo = true;
                        $hitungPromo = ($p->persen / 100) * $value->harga;
                        $value->harga_promo = $value->harga - $hitungPromo;
                        $value->promo = [
                            'm_promo_id' => $p->m_promo_id,
                            'kode' => $p->kode,
                            'promo' => $p->promo,
                            'tanggal_mulai' => $p->tanggal_mulai,
                            'jam_mulai' => $p->jam_mulai,
                            'tanggal_selesai' => $p->tanggal_selesai,
                            'jam_selesai' => $p->jam_selesai,
                            'persen' => $p->persen,
                            'nominal' => $p->nominal,
                            'qty' => $p->qty,
                            'promo_min_beli' => $p->promo_min_beli
                        ];
                    }
                }
            }
            return response()->json(['success' => true, "data" => $produk]);
        }
        else{
            return response()->json(['success' => true, "data" => []]);
        }
    }

    private function validasiSubscribe($request) {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required",
        ]);

        return $validator;
    }
    public function subscribe(Request $request) {
        try {
            $params = (array) $request->only('name', 'email', 'is_subscribed');
            $validator = $this->validasiSubscribe($request);

            // Periksa jika validasi gagal
            if ($validator->fails()) {
                return response()->json(['status_code' => 422, 'message' => $validator->errors()], 422);
            }

            $data = $this->subscribe->post($params);

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

    public function getProdukSlug(Request $request) {
        $promo = $this->promoDet->getDetailPromoAktif();
        $produk = $this->product->getBySlug($request->slug);
        $produk->variant = $this->product->getVariant($produk->id);
        $produk->foto = $this->product->getMainPhotoProduk($produk->id);
        $produk->video = $this->product->getVideo($produk->id);
        $produk->is_promo = false;
        $produk->is_flashsale = false;
        $produk->promo = [];
        $produk->harga_promo = $produk->harga;

        foreach ($produk->detail_foto as $value) {
            $value->foto = $value->media_link;

            if($value->is_video == 'ya'){
                $value->foto = Storage::url('videos/produk/' . $value->media_link);
            }
        }

        foreach ($promo as $k => $p) {
            if($p->m_produk_id === $produk->id){
                $produk->is_promo = true;
                $hitungPromo = ($p->persen / 100) * $produk->harga;
                $produk->harga_promo = $produk->harga - $hitungPromo;
                $produk->promo = [
                    'm_promo_id' => $p->m_promo_id,
                    'kode' => $p->kode,
                    'promo' => $p->promo,
                    'tanggal_mulai' => $p->tanggal_mulai,
                    'jam_mulai' => $p->jam_mulai,
                    'tanggal_selesai' => $p->tanggal_selesai,
                    'jam_selesai' => $p->jam_selesai,
                    'persen' => $p->persen,
                    'nominal' => $p->nominal,
                    'qty' => $p->qty,
                    'promo_min_beli' => $p->promo_min_beli
                ];
            }
        }

        if($produk){
            return response()->json(['status_code' => 200, 'data' => $produk], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function katalog(Request $request) {
        $params = (array) $request->all();
        $promo = $this->promoDet->getDetailPromoAktif();
        $produk = $this->product->getAllPaginate($params);

        foreach ($produk['list'] as $value) {
            $value->variant = $this->product->getVariant($value->id);
            $value->photo_product = $this->product->getPhoto($value->id);
            $value->foto = $this->product->getMainPhotoProduk($value->id);
            $value->rating = $this->produkUlasan->getUlasanByProdukId($value->id)['rataRating'];
            $value->total_terjual = $this->order->getTotalTerjual($value->id)['total_terjual'];

            $value->rowspan = count($value->variant);
            $value->is_promo = false;
            $value->is_flashsale = false;
            $value->promo = [];
            $value->harga_promo = $value->harga;
            foreach ($promo as $k => $p) {
                if($p->m_produk_id === $value->id){
                    $value->is_promo = true;
                    $hitungPromo = ($p->persen / 100) * $value->harga;
                    $value->harga_promo = $value->harga - $hitungPromo;
                    $value->promo = [
                        'm_promo_id' => $p->m_promo_id,
                        'kode' => $p->kode,
                        'promo' => $p->promo,
                        'tanggal_mulai' => $p->tanggal_mulai,
                        'jam_mulai' => $p->jam_mulai,
                        'tanggal_selesai' => $p->tanggal_selesai,
                        'jam_selesai' => $p->jam_selesai,
                        'persen' => $p->persen,
                        'nominal' => $p->nominal,
                        'qty' => $p->qty,
                        'promo_min_beli' => $p->promo_min_beli
                    ];
                }
            }
        }

        if ($produk){
            return response()->json(['status_code' => 200, 'data' => $produk], 200);
        } else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function getBrand(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->brand->getAll($params);

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

    public function getStok(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->product->stok($params);

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

    public function getRates(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->biteship->getRates($params);

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

    public function getUser(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->user->getDetailUser($params['id']);
            $data->photo = Storage::url('images/customer/' . $data->photo);

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

    public function saveUser(Request $request){
        try {
            $params = (array) $request->only('id', 'type', 'username', 'name', 'email', 'password', 'phone_code', 'phone_number', 'remember_token', 'address', 'photo', 'roles_id', 'kode', 'email_expired', 'is_active',);
            $data = $this->customer->simpan($params);

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

    public function getOrder(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->order->getOrderUer($params);

            foreach ($data['list'] as $i => $order) {
                foreach ($order['detail'] as $k => $detail) {
                    $data['list'][$i]['detail'][$k]['photo'] = $this->product->getMainPhotoProduk($detail['product_id']);
                }
            }

            return response()->json([ 'data' => $data, 'status_code' => 200 ], 200);
        } catch (\Throwable $th) {
            return response()->json([ 'message' => $th, 'status_code' => 500], 500);
        }
    }

    public function getOrderEdukasi(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->order->getOrderEdukasi($params);

            foreach ($data['list'] as $i => $item) {
                $data['list'][$i]->gambar =  Storage::url('images/edukasi/' . $item->gambar);
            }

            return response()->json([ 'data' => $data, 'status_code' => 200 ], 200);
        } catch (\Throwable $th) {
            return response()->json([ 'message' => $th, 'status_code' => 500], 500);
        }
    }

    public function getNews(Request $request){
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

    public function getCategoryNews(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->newsCategory->getAll($params);

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
            $data = $this->news->getBySlug($slug);

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

    public function getNewsTerbaru(){
        try {
            $data = $this->news->newsTerbaru();

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

    public function getComment($id){
        try {
            $data = $this->newsKomentar->getByNewsId($id);

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

    public function clickToViewNews($id){
        try {
            $data = $this->news->clickToViews($id);

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

    private function validasiKomentar($request) {
        $validator = Validator::make($request->all(), [
            "nama" => "required",
            "email" => "required",
            "komentar" => "required",
        ]);

        return $validator;
    }
    public function postComment(Request $request) {
        try {
            $params = (array) $request->only('news_id', 'nama', 'email', 'komentar', 'user_id');
            $validator = $this->validasiKomentar($request);

            // Periksa jika validasi gagal
            if ($validator->fails()) {
                return response()->json(['status_code' => 422, 'message' => $validator->errors()], 422);
            }

            $data = $this->newsKomentar->postKomentar($params);

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

    function statusOrder($status) {
        if ($status === 'ordered'){
            return 'Belum Bayar';
        } else if ($status === 'processed'){
            return 'Konfirmasi';
        } else if ($status === 'sent'){
            return 'Kirim';
        } else if ($status === 'received'){
            return 'Selesai';
        } else{
            return 'Batal';
        }
    }

    public function getUlasan($id){
        try {
            $data = $this->produkUlasan->getUlasanByProdukId($id);

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

    private function validasiUlasan($request) {
        $validator = Validator::make($request->all(), [
            "rating" => "required",
            "ulasan" => "required:min:10",
        ]);

        return $validator;
    }
    public function postUlasan(Request $request) {
        try {
            $params = (array) $request->only('m_produk_id', 't_order_id', 'rating', 'ulasan', 'user_id');
            $validator = $this->validasiUlasan($request);

            // Periksa jika validasi gagal
            if ($validator->fails()) {
                return response()->json(['status_code' => 422, 'message' => $validator->errors()], 422);
            }

            $data = $this->produkUlasan->postUlasan($params);

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

    public function cronjob(Request $request) {
        try {

            //untuk promo
            $promo = $this->promo->getPromoChanged();
            foreach ($promo as $key => $value) {
                $pPromo = [
                    'id' => $value->id,
                    'is_status' => 0
                ];

                $this->promo->updatePromo($pPromo);
            }

            //untuk voucher
            $voucher = $this->voucher->getVoucherChanged();
            foreach ($voucher as $key => $value) {
                $pVoucher = [
                    'id' => $value->id,
                    'is_status' => 0
                ];

                $this->voucher->changeStatusVoucher($pVoucher);
            }

            return response()->json([
                'data' => [],
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
