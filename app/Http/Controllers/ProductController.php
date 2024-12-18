<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Product $storeIndex)
    {
        return response()->json([
            'Status' => 200,
            'Message' => 'the request has been received',
            'data'=> $storeIndex
        ]);
    }
    public function store(Request $request,$storeIndex){
        $validateData= $request->validate([
            'name'=> 'required',
            'quantity'=>'required',
            'describtion'=>'required'
        ]);
        $product =  Product::create([
            'name' => $validateData['name'],
            'quantity'=> $validateData['quantity'],
            'describtion'=> $validateData['describtion'],
            'store_id' => $storeIndex,
        ]);
        return response()->json([
            'Status' => 200,
            'Message' => 'Product registered successfuly',
            'data' => $product
        ]);
    }
}
