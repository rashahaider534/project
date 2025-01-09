<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Cart_items;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {  
        if (!auth()->check())// التحقق من أن المستخدم مسجل الدخول
        {
        return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = auth()->user();
       
        $orders=Order::where('user_id',$user->id)->get();
        return response()->json($orders);
    }

public function createOrder()
{
    // التحقق من أن المستخدم مسجل الدخول
    if (!auth()->check()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // استرجاع المستخدم
    $user = auth()->user();
    $cartItems = Cart_items::where('user_id', $user->id)
        ->with(['product' => function ($query) {
            $query->withPivot('product_id','quantity'); // جلب الكميات من جدول الربط
        }])
        ->get();

    // التحقق من أن السلة تحتوي على منتجات
    if ($cartItems->isEmpty()) {
        return response()->json(['message' => 'Your cart is empty'], 400);
    }

    // حساب المبلغ الإجمالي
    $totalPrice = 0;
    foreach ($cartItems as $cartItem) {
        foreach ($cartItem->product as $product) {
            $totalPrice += $product->price * $product->pivot->quantity;
        }
    }
    $order = Order::create([
        'user_id' => $user->id,
        'total_price' => $totalPrice,
        
    ]);
    foreach ($cartItems as $cartItem) {
        foreach ($cartItem->product as $product) {
            // إضافة المنتج إلى order_items مع الكمية والسعر
            $order->order_items()->create([
                'product_id' => $product->id,
                'quantity' => $product->pivot->quantity,
                'price' => $product->price, // حفظ السعر في order_items
            ]);
        }
    }
    Cart_items::where('user_id', $user->id)->delete();
    return response()->json([
        'message' => 'Order successfully placed and cart cleared',
        'order_id' => $order->id,
        'total_price' => $totalPrice,
    ]);
}
    public function cancel(Request $request)
    {
        if (!auth()->check())// التحقق من أن المستخدم مسجل الدخول
        {
        return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = auth()->user();
        $orderId=$request->order_id;
        $order=Order::findorFail($orderId);
        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'ليس لديك صلاحية إلغاء هذا الطلب'], 403);
        }
       
        if($order->status =='In preparation')
        {
            $order->status = 'cancelled';
            $order->save();
            foreach ($order->order_items as $orderItem) {
                // تحديث الكمية في جدول المنتجات
                $product = $orderItem->product;
                $product->quantity += $orderItem->quantity;
                $product->save();
            }
            return response()->json(['cancelled'],200);
        }
        
        return response()->json(['cant cancel'],401);
    }
}
