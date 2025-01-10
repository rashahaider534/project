<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Store;

class SearchController extends Controller
{
    public function productSearch(Request $request)
    {
        $searchTerm = $request->input('query');

        // تحقق مما إذا كان الاستعلام فارغًا
        if (empty($searchTerm)) {
            return response()->json([
                'Status' => 400,
                'Message' => 'Search query cannot be empty',
            ]);
        }

        // البحث عن المنتجات
        $products = Product::where('name', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('description', 'LIKE', '%' . $searchTerm . '%')
            ->get();

        // تحقق مما إذا كانت النتائج فارغة
        if ($products->isEmpty()) {
            return response()->json([
                'Status' => 404,
                'Message' => 'No products found',
            ]);
        }

        return response()->json([
            'Status' => 200,
            'data' => $products,
        ]);
    }

    public function storeSearch(Request $request)
    {
        $searchTerm = $request->input('query');

        // تحقق مما إذا كان الاستعلام فارغًا
        if (empty($searchTerm)) {
            return response()->json([
                'Status' => 400,
                'Message' => 'Search query cannot be empty',
            ]);
        }

        // البحث عن المتاجر
        $stores = Store::where('name', 'LIKE', '%' . $searchTerm . '%')->get();

        // تحقق مما إذا كانت النتائج فارغة
        if ($stores->isEmpty()) {
            return response()->json([
                'Status' => 404,
                'Message' => 'No stores found',
            ]);
        }

        return response()->json([
            'Status' => 200,
            'data' => $stores,
        ]);
    }
}
