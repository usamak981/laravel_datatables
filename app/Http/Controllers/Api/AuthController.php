<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use ErrorException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $this->guard()->factory()->setTTL(5);

        if(!$token = $this->guard()->attempt($credentials)){
            return response()->json(['message' => 'Incorrect email or password'], 401);
        }elseif ($token = $this->guard()->attempt($credentials)) {
            if($this->guard()->user()->role_id == '1'){
                return $this->respondWithToken($token);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        if($this->guard()->user()){
        return response()->json($this->guard()->user());
        }else{
            return response()->json(['message' => 'token expired'], 500);
        }

        // try{
        //     $this->guard()->user();
        // }catch(UserNotDefinedException $exception){
        //     return response()->json(['message' => $exception->getMessage()]);
        // }

        return response()->json($this->guard()->user());

    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $this->guard()->factory()->setTTL(5);
        return $this->respondWithToken($this->guard()->refresh());
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
            'expires_in' => $this->guard()->factory()->getTTL() * 60 . "s"
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }


    public function payload(){

        return auth('api')->payload();
    }


    public function create(Request $request){
       
        if($this->guard()->user()){
            $rules = array(
                'name'        =>  'required',
                'email'         =>  'required',
                'password'      => 'required',
            );

            $error = Validator::make($request->all(), $rules);

            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()], 400);
            }

            $form_data = array(
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'company_id' => $request->company_id
            );
            try{
                if(User::create($form_data)){
                    return response()->json(['success' => 'User Added successfully.'], 200);    
                }else{
                    return response()->json(['failed' => 'User is not Added'] , 400);
                }
            }catch(QueryException $exception){
                return response()->json(['message' => $exception->getMessage()], 500);
            }
    }else{
        return response()->json(['failed' => 'Token Expired'] , 401);
    }


    }

    public function list(){

        if($this->guard()->user()){
            $users  = User::all();
            if ($users) {
                return response()->json([
                    'message' => 'Users returned Successfully',
                    'data' => $users
                ], 200);
            }else{
                return response()->json(['message' => 'Failed to return users'], 401);
            }
       
        }else{
        return response()->json(['failed' => 'Token Expired'] , 401);
    }


    }

    public function show(Request $request){
        
        if($this->guard()->user()){
            $rules = array(
                'user_id'        =>  'required',
            );

            $error = Validator::make($request->all(), $rules);

            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()], 400);
            }

            $user = User::find($request->user_id);

            if ($user) {
                return response()->json([
                    'message' => 'User returned Successfully',
                    'data' => $user
                ], 200);
            }else{
                return response()->json(['message' => 'Failed to return user'], 401);
            }
       
        }else{
        return response()->json(['failed' => 'Token Expired'] , 401);
    }

    }

    public function update(Request $request){

        //  dd($this->guard()->user()->save());
        if($this->guard()->user()){
            $rules = array(
                'user_id'        =>  'required',
            );

            $error = Validator::make($request->all(), $rules);

            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()], 400);
            }

            $user = User::find($request->user_id);

            try{
                if($request->name){
                    $user->name = $request->name;
                }
                if($request->email){
                    $user->email = $request->email;
                }
                if($request->password){
                    $user->password = Hash::make($request->password);
                }
                if($request->role_id){
                    $user->role_id = $request->role_id;
                }
                if($request->company_id){
                    $user->company_id = $request->company_id;
                }
                $user->update();
            }catch(ErrorException $exception){
                return response()->json(['message' => $exception->getMessage()], 500);
            }catch(QueryException $exception){
                return response()->json(['message' => $exception->getMessage()], 500);
            }


            

            if ($user) {
                return response()->json([
                    'data' => $user,
                    'message' => 'Updated Successfully'
                ], 200);
            }else{
                return response()->json(['message' => 'Not updated'], 401);
            }

    }else{
        return response()->json(['failed' => 'Token Expired'] , 401);
    }

    }

    public function destroy(Request $request){
        

        if($this->guard()->user()){
            
            $rules = array(
                'user_id'        =>  'required',
            );

            $error = Validator::make($request->all(), $rules);

            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()], 400);
            }

            try{
                User::find($request->user_id)->delete();
            }catch(FatalThrowableError $exception){
                return response()->json(['message' => $exception->getMessage()], 400);
            }

            return response()->json(['message' => 'Deleted Successfully']);

    }else{
        return response()->json(['failed' => 'Token Expired'] , 401);
    }

    }
    
}
