<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Storage;
class StoreController extends Controller
{
    public function index(){
        $store=new Store;
        $stores =$store->all();
        return response()->json([
            'Status' => 200,
            'Message' => 'the request has been received',
            'data'=> $stores
        ]);
    }

    public function store(Request $request){
        $validateData =$request->validate([
            'name' => 'nullable',
            'location' => 'nullable',
            'phone' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        $filePath=null;
        if($request->hasFile('image'))
        {
            $filName=$request->file('image')->getClientOriginalName();
            $filePath = $request->file('image')->storeAs('public/images/products',$filName);
            $fileUrl = Storage::url($filePath);
        }
        $store= new Store();
        if ($filePath) {
            $store->image = $fileUrl;
            $store->URL_image = url($filePath);
        } else {
            $store->image = null;
            $store->URL_image = null;
        }
        $store->name=$validateData['name'];
        $store->location=$validateData['location'];
        $store->phone=$validateData['phone'];
        $store->save();
        return response()->json([
            'Status' => 200,
            'Message' => 'Store registered successfuly',
            'data' => $store
        ]);
    }

    public function destory(Store $store_id){
        $store_id->delete();
        return response()->json([
            'Status' => 200,
            'Message' => 'deleted successfuly',
        ]);
    }
    public function update(Request $request,$store_id){
        $store=new Store;
        $store->find($store_id);
        if(!$store){
        return response()->json([
            'message' => 'Product not found.'
        ], 404);
    }
        $validateData=$request->validate([
            'name' => 'nullable|string',
            'location' => 'nullable|string'
        ]);
        $store->update($validateData);
        return response()->json([
            'message' => 'store updated successfully',
            'data' => $store
        ], 200);
    }
}
