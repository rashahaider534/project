<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Cart_items;
use App\Models\Order_items;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth;

class OrderController extends Controller
{ //عرض الطلبات
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
                    'product_name' => $product->name,
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
        $result = $orderItems->map(function ($item) {
            return [
                'product_name' => $item->product->name, // افترض أن لديك حقل name في نموذج Product
                'quantity' => $item->quantity,
                'price' => $item->price,

            ];
        });
        return response()->json($result);
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

    public function getOrderdriver(Request $request)
    {
        $orders = Order::where('driver_id', null)->get();
        $drivers = User::where('role', 'driver')->take(5)->get(); // نأخذ أول 5 سائقين فقط
        $driverCount = $drivers->count();
        if ($driverCount == 0) {
            return response()->json(['error' => 'لا يوجد سائقين مسجلين في النظام'], 400);
        }
        // توزيع الطلبات على السائقين بشكل دوري
        foreach ($orders as $index => $order) {
            $driver = $drivers[$index % $driverCount];
            $order->driver_id = $driver->id;  // إسناد السائق للطلب
            $order->save();  // حفظ التغييرات
        }
        $driver = $request->user(); // المستخدم الذي دخل باستخدام الـ Token
        // جلب جميع الطلبات المسندة لهذا السائق
        $orders = Order::where('driver_id', $driver->id)->get(); // استخدام driver_id بناءً على المستخدم الذي سجل دخوله
        return response()->json($orders);
    }

    public function updatestatus(Request $request)
    {
        $orderId = $request->order_id;
        $order = Order::findOrFail($orderId);
        $driver = $request->user();
        if ($order->driver_id !== $driver->id) {
            return response()->json(['error' => 'أنت غير مخول لتغيير حالة هذا الطلب'], 403);
        }
        if ($order->status != 'cancelled') {
            $order->status = $request->status;
            $order->save();
            return response()->json(['message' => 'Order status updated successfully.']);
        }
        if ($order->status == 'cancelled') {
            return response()->json(['message' => 'Order cancelled cant update status '], 400);
        }
    }
}
