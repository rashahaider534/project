<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('userInformation',[UserController::class,'update'] ); //لإدخال بيانات المستخدم

Route::get('stores',[StoreController::class,'index'] );  // لعرض المتاجر

Route::post('stores',[StoreController::class,'store'] );  // لتخزين متجر

Route::delete('stores/{store_id}',[StoreController::class,'destory'] ); // لحزف متجر

Route::put('stores/{store_id}',[StoreController::class,'update'] ); // للتعديل على بيانات متجر

Route::get('product/{storeIndex}',[ProductController::class,'index']); //لعرض منتجات متجر

Route::post('product/{storeIndex}',[ProductController::class,'store']);//لتخزين منتج

Route::post('register', [AuthController::class,'register']);

Route::post('login', [AuthController::class,'login']);

Route::post('logout', [AuthController::class,'logout']);

Route::post('me', [AuthController::class,'me']);

Route::post('refresh', [AuthController::class,'refresh']);

