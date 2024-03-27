<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\RoleController;
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
    Route::group(['prefix' => 'auth'], function (){
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/me', [AuthController::class, 'me']);
    });
    Route::group(['prefix' => 'v1'], function (){
        Route::group(['prefix' => 'user'], function (){
            Route::get('/', [UserController::class, 'getData']);
            Route::get('/{id}', [UserController::class, 'getDetailUser']);
            Route::post('/', [UserController::class, 'create']);
            Route::post('/{id}', [UserController::class, 'update']);
            Route::post('/status', [UserController::class, 'changeStatus']);
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
            Route::get('/{id}', [KategoriController::class, 'getDetailKategori']);
            Route::post('/', [KategoriController::class, 'create']);
            Route::post('/{id}', [KategoriController::class, 'update']);
            Route::post('/status', [KategoriController::class, 'changeStatus']);
        });
    });
});

Route::group(['middleware' => 'public'], function (){

});
