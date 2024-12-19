<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function update(Request $request)
    {
    $data =  $request->validate([
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'location' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg'
        ]);


        if($request->hasFile('image'))
        {
            $filName=$request->file('image')->getClientOriginalName();
            $filePath = $request->file('image')->storeAs('public/images/users',$filName);
        }

        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->location = $request->location;
        $user->image = $filePath;
        $user->save();

        return response()->json([
            'Status' => 200,
            'Message' => 'User registered successfuly',
            'user_id' => $user->id
        ]);
    }
    public function index(User $user_id)
    {
        if ($user_id->image && Storage::exists($user_id->image)) {
            $image= response()->download(storage_path('app/' . $user_id->image));
        }
        return response()->json([
            'Status' => 200,
            'Message' => 'User registered successfuly',
            'data' => [
                'first_name'=>$user_id->first_name,
                'last_name'=>$user_id->last_name,
                'location'=>$user_id->location,
                'phone'=>$user_id->phone,
            ]
        ]) . $image;
    }

}
