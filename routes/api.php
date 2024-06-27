<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EdukasiController;
use App\Http\Controllers\EdukasiKategoriController;
use App\Http\Controllers\EdukasiSliderController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsKategoriController;
use App\Http\Controllers\NewsKomentarController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\PromoSliderController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\StokKategoriController;
use App\Http\Controllers\StokKeluarController;
use App\Http\Controllers\StokMasukController;
use App\Http\Controllers\StokUpdateController;
use App\Http\Controllers\UserController;

use App\Mail\VerifikasiEmail;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'api'], function (){
    Route::group(['prefix' => 'v1'], function (){
        Route::group(['prefix' => 'auth'], function (){
            Route::post('/login', [AuthController::class, 'login']);
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::post('/me', [AuthController::class, 'me']);
            Route::get('/checkAuthorization',  [AuthController::class, 'checkToken']);
        });

        Route::group(['prefix' => 'dashboard'], function (){
            Route::get('/counterPesanan', [DashboardController::class, 'counterPesanan']);
            Route::get('/pendapatan', [DashboardController::class, 'pendapatan']);
            Route::get('/penjualanhariini', [DashboardController::class, 'penjualanhariini']);
        });

        Route::group(['prefix' => 'user'], function (){
            Route::get('/', [UserController::class, 'getData']);
            Route::get('/{id}', [UserController::class, 'getDetailUser']);
            Route::post('/', [UserController::class, 'create']);
            Route::post('/{id}', [UserController::class, 'update']);
            Route::put('/status', [UserController::class, 'changeStatus']);
        });

        Route::group(['prefix' => 'customer'], function (){
            Route::get('/', [CustomerController::class, 'getData']);
            Route::get('/{id}', [CustomerController::class, 'getDataById']);
            Route::post('/save', [CustomerController::class, 'simpan']);
            Route::post('/status', [CustomerController::class, 'changeStatus']);
        });

        Route::group(['prefix' => 'address'], function (){
            Route::get('/', [AddressController::class, 'getData'])->name('getData');
            Route::get('/edit', [AddressController::class, 'getAddressById'])->name('getDataById');
            Route::post('/save', [AddressController::class, 'saveAddress'])->name('simpan');
            Route::post('/update', [AddressController::class, 'updateAddress'])->name('simpan');
            Route::post('/delete', [AddressController::class, 'deleteAddress'])->name('deleteAddress');
        });

        Route::group(['prefix' => 'role'], function (){
            Route::get('/', [RoleController::class, 'getData']);
            Route::get('/{id}', [RoleController::class, 'getDetailRole']);
            Route::post('/', [RoleController::class, 'create']);
            Route::post('/{id}', [RoleController::class, 'update']);
            Route::post('/status', [RoleController::class, 'changeStatus']);
        });

        Route::group(['prefix' => 'kategori'], function (){
            Route::get('/', [KategoriController::class, 'getData']);
            Route::get('/{id}', [KategoriController::class, 'getDataById']);
            Route::get('/detail/{id}', [KategoriController::class, 'getDetail']);
            Route::post('/save', [KategoriController::class, 'simpan']);
            Route::post('/status', [KategoriController::class, 'changeStatus']);
        });

        Route::group(['prefix' => 'brand'], function (){
            Route::get('/', [BrandController::class, 'getData']);
        });

        Route::group(['prefix' => 'promo'], function (){
            Route::get('/', [PromoController::class, 'getData']);
            Route::get('/{id}', [PromoController::class, 'getDataById']);
            Route::post('/save', [PromoController::class, 'simpan']);
            Route::post('/status', [PromoController::class, 'changeStatus']);
        });

        Route::group(['prefix' => 'edukasi'], function (){
            Route::group(['prefix' => 'daftar'], function (){
                Route::get('/', [EdukasiController::class, 'getData']);
                Route::get('/{id}', [EdukasiController::class, 'getDataById']);
                Route::get('/detail/{id}', [EdukasiController::class, 'getDetail']);
                Route::post('/save', [EdukasiController::class, 'simpan']);
                Route::post('/status', [EdukasiController::class, 'changeStatus']);
            });

            Route::group(['prefix' => 'kategori'], function (){
                Route::get('/', [EdukasiKategoriController::class, 'getData']);
                Route::get('/{id}', [EdukasiKategoriController::class, 'getDataById']);
                Route::get('/detail/{id}', [EdukasiKategoriController::class, 'getDetail']);
                Route::post('/save', [EdukasiKategoriController::class, 'simpan']);
                Route::post('/status', [EdukasiKategoriController::class, 'changeStatus']);
            });

            Route::group(['prefix' => 'slider'], function (){
                Route::get('/', [EdukasiSliderController::class, 'getData']);
                Route::get('/{id}', [EdukasiSliderController::class, 'getDataById']);
                Route::post('/save', [EdukasiSliderController::class, 'simpan']);
                Route::post('/status', [EdukasiSliderController::class, 'changeStatus']);
                Route::post('/moveSlider', [EdukasiSliderController::class, 'moveSlider']);
            });
        });

        Route::group(['prefix' => 'voucher'], function (){
            Route::get('/', [VoucherController::class, 'getData']);
            Route::get('/{id}', [VoucherController::class, 'getDataById']);
            Route::post('/save', [VoucherController::class, 'simpan']);
            Route::post('/status', [VoucherController::class, 'changeStatus']);
        });

        Route::group(['prefix' => 'promo-slider'], function (){
            Route::get('/', [PromoSliderController::class, 'getData']);
            Route::get('/{id}', [PromoSliderController::class, 'getDataById']);
            Route::post('/save', [PromoSliderController::class, 'simpan']);
            Route::post('/status', [PromoSliderController::class, 'changeStatus']);
            Route::post('/moveSlider', [PromoSliderController::class, 'moveSlider']);
        });

        Route::group(['prefix' => 'slider'], function (){
            Route::get('/', [SliderController::class, 'getData']);
            Route::get('/{id}', [SliderController::class, 'getDataById']);
            Route::post('/save', [SliderController::class, 'simpan']);
            Route::post('/status', [SliderController::class, 'changeStatus']);
            Route::post('/moveSlider', [SliderController::class, 'moveSlider']);
        });

        Route::group(['prefix' => 'setting'], function (){
            Route::get('/', [SettingController::class, 'getSetting']);
            Route::get('/{id}', [SettingController::class, 'getDataById']);
            Route::post('/save', [SettingController::class, 'simpan']);
            Route::post('/updatePopup', [SettingController::class, 'updatePopUp']);
        });

        Route::group(['prefix' => 'produk'], function (){
            Route::get('/', [ProdukController::class, 'getData']);
            Route::get('/{id}', [ProdukController::class, 'getDataById']);
            Route::get('/byKategori/{id}', [ProdukController::class, 'getProdukByKategori']);
            Route::get('/detail/{id}', [ProdukController::class, 'getDetail']);
            Route::get('/photo/{id}', [ProdukController::class, 'getPhoto']);
            Route::get('/variant/{id}', [ProdukController::class, 'getVariant']);
            Route::get('/variant/type/{type}', [ProdukController::class, 'varian']);
            Route::post('/save', [ProdukController::class, 'simpan']);
            Route::post('/ubahStatus', [ProdukController::class, 'ubahStatus']);
            Route::post('/prosesVariant', [ProdukController::class, 'prosesVariant']);
            Route::post('/prosesPhotoVarian', [ProdukController::class, 'prosesPhotoVarian']);
            Route::post('/updateStok', [ProdukController::class, 'updateStok']);
            Route::post('/updateStokProduk', [ProdukController::class, 'updateStokProduk']);
        });

        Route::group(['prefix' => 'order'], function (){
            Route::get('/', [OrderController::class, 'getData']);
            Route::get('/{id}', [OrderController::class, 'getDataById']);
            Route::post('/save', [OrderController::class, 'simpan']);
            Route::post('/status', [OrderController::class, 'changeStatus']);
            Route::post('/pay', [OrderController::class, 'createOrder'])->name('createOrder');
            Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
        });


        Route::group(['prefix' => 'xendit'], function (){
            Route::post('/callback', [OrderController::class, 'xenditCallback']);
        });

        Route::group(['prefix' => 'news'], function (){
            Route::get('/', [NewsController::class, 'getData']);
            Route::get('/{id}', [NewsController::class, 'getDataById']);
            Route::get('/detail/{id}', [NewsController::class, 'getDetail']);
            Route::post('/save', [NewsController::class, 'simpan']);
            Route::post('/status', [NewsController::class, 'changeStatus']);
        });

        Route::group(['prefix' => 'news-kategori'], function (){
            Route::get('/', [NewsKategoriController::class, 'kategori']);
            Route::get('/{id}', [NewsKategoriController::class, 'getDataById']);
            Route::post('/save', [NewsKategoriController::class, 'simpan']);
        });

        Route::group(['prefix' => 'news-komentar'], function (){
            Route::get('/', [NewsKomentarController::class, 'kategori']);
            Route::get('/{id}', [NewsKomentarController::class, 'getDataById']);
            Route::post('/post', [NewsKomentarController::class, 'post']);
        });

        Route::group(['prefix' => 'stok'], function (){
            Route::get('/available', [StokMasukController::class, 'getAvailable']);

            Route::group(['prefix' => 'kategori'], function (){
                Route::get('/', [StokKategoriController::class, 'getData']);
                Route::get('/{id}', [StokKategoriController::class, 'getDataById']);
                Route::get('/type/{type}', [StokKategoriController::class, 'getDataByType']);
                Route::post('/save', [StokKategoriController::class, 'simpan']);
                Route::post('/status', [StokKategoriController::class, 'changeStatus']);
            });
            Route::group(['prefix' => 'masuk'], function (){
                Route::get('/', [StokMasukController::class, 'getData']);
                Route::get('/{id}', [StokMasukController::class, 'getDataById']);
                Route::post('/save', [StokMasukController::class, 'simpan']);
                Route::post('/status', [StokMasukController::class, 'changeStatus']);
            });
            Route::group(['prefix' => 'keluar'], function (){
                Route::get('/', [StokKeluarController::class, 'getData']);
                Route::get('/{id}', [StokKeluarController::class, 'getDataById']);
                Route::post('/save', [StokKeluarController::class, 'simpan']);
                Route::post('/status', [StokKeluarController::class, 'changeStatus']);
            });
            Route::group(['prefix' => 'opname'], function (){
                Route::get('/', [StokUpdateController::class, 'getData']);
                Route::get('/{id}', [StokUpdateController::class, 'getDataById']);
                Route::post('/save', [StokUpdateController::class, 'simpan']);
                Route::post('/status', [StokUpdateController::class, 'changeStatus']);
            });
        });

        Route::prefix('public')->group(function (){
            // user post
            Route::get('/checkEmail', [UserController::class, 'checkEmail']);
            Route::post('/register', [UserController::class, 'register']);
            Route::post('/login', [UserController::class, 'login']);

            // Account
            Route::get('/user', [SiteController::class, 'getUser']);
            Route::post('/user-save', [SiteController::class, 'saveUser']);
            Route::get('/orders', [SiteController::class, 'getOrder']);

            // public
            Route::get('/settingPopup', [SettingController::class, 'getSetting']);
            Route::get('/popup', [PromoSliderController::class, 'slider']);
            Route::get('/kategori', [KategoriController::class, 'kategori'])->name('kategori');
            Route::get('/produk', [SiteController::class, 'getProduct'])->name('produk');
            Route::get('/produkPromo', [SiteController::class, 'getProductPromo']);
            Route::get('/getProdukSlug', [SiteController::class, 'getProdukSlug'])->name('getProdukSlug');
            Route::post('/getLastSeenProduk', [SiteController::class, 'getLastSeenProduk']);
            Route::get('/katalog', [SiteController::class, 'katalog'])->name('katalog');
            Route::get('/slider', [SiteController::class, 'slider'])->name('slider');
            Route::get('/brand', [SiteController::class, 'getBrand']);
            Route::get('/category-news', [SiteController::class, 'getCategoryNews']);

            Route::get('/stok', [SiteController::class, 'getStok']);

            Route::prefix('news')->group(function (){
                Route::get('/', [SiteController::class, 'getNews']);
                Route::post('/view/{id}', [SiteController::class, 'clickToViewNews']);
                Route::get('/getNewsTerbaru', [SiteController::class, 'getNewsTerbaru']);
                Route::get('/{slug}', [SiteController::class, 'getDataBySlug']);
                Route::get('/comment/{id}', [SiteController::class, 'getComment']);
                Route::post('/post', [SiteController::class, 'postComment']);
            });

            // Edukasi
            Route::prefix('edukasi')->group(function (){
                Route::get('/slider', [EdukasiSliderController::class, 'slider']);
                Route::get('/kategori', [EdukasiKategoriController::class, 'kategori']);
                Route::get('/list', [EdukasiController::class, 'edukasi']);
                Route::get('/{slug}', [EdukasiController::class, 'getDataBySlug']);
                Route::post('/pay', [EdukasiController::class, 'pay']);
            });
        });

        // Cart
        Route::prefix('cart')->group(function (){
            Route::get('/get', [CartController::class, 'getCart'])->name('getCart');
            Route::post('/add', [CartController::class, 'addCart'])->name('addCart');
            Route::post('/update', [CartController::class, 'updateCart'])->name('updateCart');
            Route::post('/delete', [CartController::class, 'deleteCart'])->name('deleteCart');
        });


        // Address
        Route::prefix('address')->group(function (){
            Route::get('/get', [AddressController::class, 'getAddress'])->name('getAddress');
            Route::get('/edit', [AddressController::class, 'getAddressById'])->name('getAddressById');
            Route::post('/save', [AddressController::class, 'saveAddress'])->name('saveAddress');
            Route::post('/update', [AddressController::class, 'updateAddress'])->name('updateAddress');
            Route::post('/delete', [AddressController::class, 'deleteAddress'])->name('deleteAddress');
            Route::get('/main', [AddressController::class, 'getMainAddress'])->name('mainAddress');
        });

        // Region
        Route::prefix('region')->group(function (){
            Route::get('/village', [RegionController::class, 'village'])->name('village');
            Route::get('/subdistrict', [RegionController::class, 'subdistrict'])->name('subdistrict');
            Route::get('/city', [RegionController::class, 'city'])->name('city');
            Route::get('/province', [RegionController::class, 'province'])->name('province');
        });

        Route::get('/make-password', function() {
            $password = bcrypt('123456');

            return response([
                'message' => 'Berhasil membuat password',
                'password' => $password
            ]);
        });

        Route::post('/send-email', function() {
            $data = [
                'subject' => 'Subjek',
                'message' => 'Ini adalah contoh email custom'
            ];

            Mail::to('sgalih1234@gmail.com')->send(new VerifikasiEmail($data));

            return response()->json(['message' => 'Email sent successfully!']);
        });
    });
});

