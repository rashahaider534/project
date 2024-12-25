<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authrequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use DragonCode\Contracts\Cashier\Http\Request;
use Illuminate\Support\Facades\DB;
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Authrequest $request)
    {
        $data=$request->validated();
        $data['password']=bcrypt($request->password);
        $user=User::Create($data);
        //لعدم تكرار الكود نستخدم login
        $careden=['phone'=>$user->phone,'password'=>$request->password];
        return $this->login($careden);

        // $token = auth()->attempt(['phone'=>$user->phone,'password'=>$request->password]);
        // if (!$token  ) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }
        // return $this->respondWithToken($token);

    }

    public function login(array $careden=null)
    {
        $credentials = request(['phone', 'password']);
        $attempt=!empty($careden)?$careden:$credentials;
        if (! $token = auth()->attempt($attempt)) 
        {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        //  return $this->respondWithToken($token);
         return response_data($this->respondWithToken($token),'login Successful');
    }

    public function updateProfile(UserRequest $request)
    {
       
        $data = $request->validated();
          if (!auth()->check())// التحقق من أن المستخدم مسجل الدخول
         {
         return response()->json(['error' => 'Unauthorized'], 401);
         }
         $user = auth()->user();// الحصول على المستخدم الحالي    
        // تحديث البيانات الأساسية مثل الاسم والموقع
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->location = $request->location;      
        // إذا تم إرسال صورة، نقوم بتخزينها وتحديث مسارها في قاعدة البيانات
        if ($request->hasFile('image')) {
            $fileName = $request->file('image')->getClientOriginalName();
            $filePath = $request->file('image')->storeAs('public/images/users', $fileName);
            $user->image = $filePath;
           
        }
        $user->save();
        return response()->json([
            'Status' => 200,
            'Message' => 'User profile updated successfully',           
        ]);
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\Json  Response
     */
    public function me()
    {
       
    $user=auth()->user();
    return response_data($user);



    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
    return response_data([],'Successfully logged out');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());


    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

  
   
}
?>
