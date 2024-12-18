<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
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
            'name' => 'required',
            'location' => 'required'
        ]);
        $store =  Store::create([
            'name' => $validateData['name'],
            'location' => $validateData['location']
        ]);
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
