<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authrequest;
use App\Models\User;

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
        if (! $token = auth()->attempt($attempt)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

      //  return $this->respondWithToken($token);
      return response_data($this->respondWithToken($token),'login Successful');
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\Json  Response
     */
    public function me()
    {
       // return response()->json(auth()->user());
       $user=auth()->user()->only('phone','password');
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

       // return response()->json(['message' => 'Successfully logged out']);
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
