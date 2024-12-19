<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    public function index($store_id)
    {
        $data=Product::where('store_id',$store_id)->get();
        return response()->json([
            'Status' => 200,
            'Message' => 'the request has been received',
            'data'=> $data,
        ]);
    }
    public function store(Request $request,$storeIndex){
        $validateData= $request->validate([
            'name'=> 'nullable',
            'quantity'=>'nullable',
            'describtion'=>'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',

        ]);
        if($request->hasFile('image'))
        {
            $filName=$request->file('image')->getClientOriginalName();
            $filePath = $request->file('image')->storeAs('public/images/products',$filName);
        }
        $product = new Product ;
        $product->name=$validateData['name'];
        $product->quantity=$validateData['quantity'];
        $product->description=$validateData['describtion'];
        $product->store_id=$storeIndex;
        $product->image=$filePath;
        $product->save();
        return response()->json([
            'Status' => 200,
            'Message' => 'Product registered successfuly',
            'data' =>$product->id
        ]);
    }
}
