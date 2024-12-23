<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function update( $id,Request $request)
    {
        $data = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg'
        ]);
        $user = User::find($id);

        if (!$user) {
            return response()->json(['Status' => 404, 'Message' => 'User not found']);
        }
        $filePath=null;
        if ($request->hasFile('image')) {
            $fileName = $request->file('image')->getClientOriginalName();
            $filePath = $request->file('image')->storeAs('public/images/users', $fileName);
        }

        if ($filePath) {
            $user->image = $filePath;
            $user->URL_image = url($filePath);
        } else {
            $user->image = null;
            $user->URL_image = null;
        }
        $user->update([
            $user->first_name=$request->firt_name,
            $user->last_name=$request->last_name,
            $user->location=$request->location,

        ]);

        return response()->json([
            'Status' => 200,
            'Message' => 'User updated successfully',
            'user_id' => $user->id
        ]);
        }
    public function index(User $user_id)
    {

        return response()->json([
            'Status' => 200,
            'Message' => 'User registered successfuly',
            'data' => [
                'first_name'=>$user_id->first_name,
                'last_name'=>$user_id->last_name,
                'location'=>$user_id->location,
                'phone'=>$user_id->phone,
                'phone'=>$user_id->URL_image,
            ]
        ]) ;
    }

}
