<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart_items;
use App\Models\Order_items;
use Illuminate\Http\Request;

class OrderController extends Controller
{//عرض الطلبات
    public function index()
    {
        if (!auth()->check()) // التحقق من أن المستخدم مسجل الدخول
        {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = auth()->user();

        $orders = Order::where('user_id', $user->id)->get();
        return response()->json($orders);
    }
//انشاء الطلب
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
                $query->withPivot('product_id', 'quantity'); // جلب الكميات من جدول الربط
            }])
            ->get();

        // التحقق من أن السلة تحتوي على منتجات
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty'], 400);
        }
        // حساب المبلغ الإجمالي
        $totalPrice = 0;
        $orderItemsData = []; // تخزين بيانات order_items مؤقتاً
        foreach ($cartItems as $cartItem) {
            foreach ($cartItem->product as $product) {
                $productTotalPrice = $product->price * $product->pivot->quantity;
                $totalPrice += $productTotalPrice;  // إضافة المجموع إلى totalPrice
                // إضافة بيانات المنتج إلى orderItemsData
                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $product->pivot->quantity,
                    'price' => $productTotalPrice, // حفظ السعر في order_items
                ];
            }
        }

        // إنشاء الطلب
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $totalPrice,
        ]);

        // إضافة العناصر إلى جدول order_items
        foreach ($orderItemsData as $itemData) {
            $order->order_items()->create($itemData);
        }
        Cart_items::where('user_id', $user->id)->delete();
        return response()->json([
            'message' => 'Order successfully placed and cart cleared',
            'order_id' => $order->id,
            'total_price' => $totalPrice,
        ]);
    }
//عرض المنتجات في الطلب
    public function getProductsFromOrder(Request $request)
    {
        if (!auth()->check()) // التحقق من أن المستخدم مسجل الدخول
        {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = auth()->user();
        $orderId = $request->order_id;
        $order = Order::findorFail($orderId);
        $orderItems = Order_items::where('order_id', $orderId)->get();
        return response()->json($orderItems);
    }

//الغاء الطلب
    public function cancel(Request $request)
    {
        if (!auth()->check()) // التحقق من أن المستخدم مسجل الدخول
        {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = auth()->user();
        $orderId = $request->order_id;
        $order = Order::findorFail($orderId);
        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'ليس لديك صلاحية إلغاء هذا الطلب'], 403);
        }

        if ($order->status == 'In preparation') {
            $order->status = 'cancelled';
            $order->save();
            foreach ($order->order_items as $orderItem) {
                // تحديث الكمية في جدول المنتجات
                $product = $orderItem->product;
                $product->quantity += $orderItem->quantity;
                $product->save();
            }
            return response()->json(['cancelled'], 200);
        }

        return response()->json(['cant cancel'], 401);
    }
}
