<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        return response()->json(User::with(['orders'])->get(), 200);
    }
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->get('email'),
            'password' => $request->get('password'),
        ];
        
        $status = 401;
        $response = ['error' => 'Unauthorised'];
        
        if (Auth::attempt($credentials)) {
            $status = 200;
            $response = [
                'token' => Auth::user()->createToken('authToken')->accessToken,
                'user' => Auth::user()
            ];
            // var_dump($response);
        }
        
        return response()->json($response, $status);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        
        $user = User::create($input);

        $success = [
            'user' => $user,
            'token' => $user->createToken('authToken')->accessToken,
        ];
        
        return response()->json($success);
    }
    
    public function show(User $user)
    {
        return response()->json($user,200);
    }
    
    public function showOrders(User $user)
    {
        return response()->json($user->orders()->with(['product'])->get(),200);
    }
}