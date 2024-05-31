<?php

use App\Http\Controllers\AddressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsKategoriController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\StokKategoriController;
use App\Http\Controllers\StokKeluarController;
use App\Http\Controllers\StokMasukController;
use App\Http\Controllers\UserController;
use App\Models\StokUpdate;

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

        Route::group(['prefix' => 'voucher'], function (){
            Route::get('/', [VoucherController::class, 'getData']);
            Route::get('/{id}', [VoucherController::class, 'getDataById']);
            Route::post('/save', [VoucherController::class, 'simpan']);
            Route::post('/status', [VoucherController::class, 'changeStatus']);
        });

        Route::group(['prefix' => 'slider'], function (){
            Route::get('/', [SliderController::class, 'getData']);
            Route::get('/{id}', [SliderController::class, 'getDataById']);
            Route::post('/save', [SliderController::class, 'simpan']);
            Route::post('/status', [SliderController::class, 'changeStatus']);
            Route::post('/moveSlider', [SliderController::class, 'moveSlider']);
        });

        Route::group(['prefix' => 'produk'], function (){
            Route::get('/', [ProdukController::class, 'getData']);
            Route::get('/{id}', [ProdukController::class, 'getDataById']);
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
                Route::get('/', [StokUpdate::class, 'getData']);
                Route::get('/{id}', [StokUpdate::class, 'getDataById']);
                Route::post('/save', [StokUpdate::class, 'simpan']);
                Route::post('/status', [StokUpdate::class, 'changeStatus']);
            });
        });

        Route::prefix('public')->group(function (){
            // user post
            Route::post('/checkEmail', [UserController::class, 'checkEmail']);
            Route::post('/register', [UserController::class, 'register']);
            Route::post('/login', [UserController::class, 'login']);

            // public
            Route::get('/kategori', [KategoriController::class, 'kategori'])->name('kategori');
            Route::get('/produk', [SiteController::class, 'getProduct'])->name('produk');
            Route::get('/getProdukSlug', [SiteController::class, 'getProdukSlug'])->name('getProdukSlug');
            Route::get('/katalog', [SiteController::class, 'katalog'])->name('katalog');
            Route::get('/slider', [SiteController::class, 'slider'])->name('slider');
            Route::get('/brand', [SiteController::class, 'getBrand']);

            Route::get('/stok', [SiteController::class, 'getStok']);
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
    });
});

Route::prefix('v1')->group(function (){

});
