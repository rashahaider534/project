<?php

namespace App\Http\Controllers;

use App\Models\Cart_items;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    //تابع اضافة المنتج الى السلة
    public function addtocart(Request $request)
    {
        if (!auth()->check()) // التحقق من أن المستخدم مسجل الدخول
        {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = auth()->user();
        $cartitem = Cart_items::firstOrCreate(['user_id' => $user->id]);
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404); // في حال لم يتم العثور على المنتج
        }
        if ($request->quantity > $product->quantity) {
            return response()->json(['message' => 'Not enough stock available'], 400);
        }
        if ($product) {
            $newquantity = $product->quantity - $request->quantity; // خصم الكمية المطلوبة من المخزون
            if ($newquantity < 0) {
                return response()->json(['message' => 'Not enough stock available'], 400);
            }
            $product->quantity = $newquantity;
            $product->save();
        }
        $cartitem->product()->attach($product->id, ['quantity' => $request->quantity]);
        return response()->json(['message' => 'Product added to cart']);
    }
    //استعراض محتوى السلة
    public function getCart()
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = auth()->user();
        $cartItems = Cart_items::where('user_id', $user->id)
            ->with(['product' => function ($query) {
                $query->withPivot('product_id', 'quantity'); // مثال لتحديد الأعمدة التي تريدها
            }])->get();

        $totalPrice = 0;
        $cartItemsData = $cartItems->map(function ($cartItem) use (&$totalPrice) {
            return $cartItem->product->map(function ($product) use (&$totalPrice) {
                $productTotalPrice = $product->price * $product->pivot->quantity;
                $totalPrice += $productTotalPrice;
                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'store_id' => $product->store_id,
                    'quantity' => $product->pivot->quantity, // الوصول إلى الكمية من جدول الربط
                    'total_price' => $productTotalPrice, // السعر الإجمالي لكل منتج
                ];
            });
        })->flatten(1);
        return response()->json([
            'cart_items' => $cartItemsData,
            'total_price' => $totalPrice // إضافة السعر النهائي للسلة
        ]);
    }

    //ازالة المنتج من السلة
    public function removeProductFromCart(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = auth()->user();
        $productId = $request->product_id;
        if (!$productId) {
            return response()->json(['error' => 'Product ID is required.'], 400);
        }
        $cartItem = Cart_items::where('user_id', $user->id)
            ->whereHas('product', function ($query) use ($productId) {
                $query->where('product_id', $productId, 'quantity');
            })
            ->first();
        if ($cartItem) {
            $product = $cartItem->product()->wherePivot('product_id', $productId)->first();
            $quantityInCart = $product->pivot->quantity;
            $actualProduct = Product::find($productId);
            $actualProduct->quantity += $quantityInCart;
            $actualProduct->save();
            $cartItem->product()->detach($productId);
            return response()->json(['message' => 'Product remove in cart.'], 200);
        } else {
            return response()->json(['error' => 'Product not found in cart.'], 404);
        }
    }
    //تحديث كمية المنتج
    public function updatecart(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = auth()->user();
        $productId = $request->product_id;
        $newQuantity = $request->quantity;
        if (!$productId || !$newQuantity) {
            return response()->json(['error' => 'Product ID and quantity are required.'], 400);
        }
        // الحصول على السلة التي تحتوي على المنتج
        $cartItem = Cart_items::where('user_id', $user->id)
            ->whereHas('product', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })->first();
        if ($cartItem) {
            $product = $cartItem->product()->wherePivot('product_id', $productId)->first();
            $actualProduct = Product::find($productId);
            if ($newQuantity > $actualProduct->quantity) {
                return response()->json(['message' => 'Not enough stock available'], 400);
            }
            // تحديث الكمية في السلة
            $cartItem->product()->updateExistingPivot($productId, ['quantity' => $newQuantity]);
            // تحديث الكمية المتبقية في المخزون
            $quantityInCart = $product->pivot->quantity;
            $actualProduct->quantity -= ($newQuantity - $quantityInCart);
            $actualProduct->save();
            return response()->json(['message' => 'Product quantity updated successfully']);
        } else {
            return response()->json(['error' => 'Product not found in cart.'], 404);
        }
    }
}
