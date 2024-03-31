<?php

use App\Http\Controllers\AddressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;

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

        Route::group(['prefix' => 'user'], function (){
            Route::get('/', [UserController::class, 'getData']);
            Route::get('/{id}', [UserController::class, 'getDetailUser']);
            Route::post('/', [UserController::class, 'create']);
            Route::post('/{id}', [UserController::class, 'update']);
            Route::put('/status', [UserController::class, 'changeStatus']);
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

        Route::prefix('public')->group(function (){
            // user post
            Route::post('/checkEmail', [UserController::class, 'checkEmail']);
            Route::post('/register', [UserController::class, 'register']);
            Route::post('/login', [UserController::class, 'login']);

            // public
            Route::get('/kategori', [KategoriController::class, 'kategori'])->name('kategori');
            Route::get('/produk', [ProdukController::class, 'produk'])->name('produk');
            Route::get('/getProdukSlug', [ProdukController::class, 'getProdukSlug'])->name('getProdukSlug');
            Route::get('/katalog', [ProdukController::class, 'katalog'])->name('katalog');
            Route::get('/slider', [SiteController::class, 'slider'])->name('slider');
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
                'message' => 'Berhasil melakukan manipulasi storage',
                'password' => $password
            ]);
        });
    });
});

Route::prefix('v1')->group(function (){

});
