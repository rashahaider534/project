<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
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
Route::middleware('auth:api')->post('/update-profile', [AuthController::class, 'updateProfile']);
 //لإدخال بيانات المستخدم
Route::get('stores',[StoreController::class,'index'] );  // لعرض المتاجر

Route::post('stores',[StoreController::class,'store'] );  // لتخزين متجر

Route::delete('stores/{store_id}',[StoreController::class,'destory'] ); // لحذف متجر

Route::put('stores/{store_id}',[StoreController::class,'update'] ); // للتعديل على بيانات متجر

Route::get('product/{store_id}',[ProductController::class,'index']); //لعرض منتجات متجر

Route::post('product/{store_id}',[ProductController::class,'store']);//لتخزين منتج

Route::post('register', [AuthController::class,'register']);

Route::post('login', [AuthController::class,'login']);

Route::post('logout', [AuthController::class,'logout']);

Route::post('me', [AuthController::class,'me']);

Route::post('refresh', [AuthController::class,'refresh']);
//order
Route::get('orders', [OrderController::class, 'index']);//عرض الطلبات

Route::post('orders/getProductsFromOrder', [OrderController::class, 'getProductsFromOrder']);//عرض المنتجات في الطلب

Route::middleware('auth:api')->get('orders/createOrder',[OrderController ::class, 'createOrder']);//انشاء طلب

Route::post('orders/updatestatus',[OrderController ::class, 'updatestatus']);

Route::post('orders/cancel',[OrderController::class,'cancel']);//الغاء طلب
//cart
Route::middleware('auth:api')->post('cart/add', [CartController::class, 'addtocart']);//الاضافة الى السلة

Route::middleware('auth:api')->get('cart/get',[CartController::class, 'getCart']);//عرض محتوى السلة

Route::middleware('auth:api')->post('cart/removeproduct',[CartController::class, 'removeProductFromCart']);//ازالة المنتج من السلة

Route::middleware('auth:api')->post('cart/updatecart',[CartController::class, 'updatecart']);//تعديل الكمية للمنتج داخل السلة
