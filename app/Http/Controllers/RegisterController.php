<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
    public function register(Request $request){
        $register = $request->validate([
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $emailCheck = User::where(['email' => $request['email']])->first();

        if($emailCheck){
            return response()->json(["response" => ["code" => "2", "success" => "-1",
            "message" => "Email already registered."]]);
        }else{
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ]);
    
            if(!Auth::attempt($register)){
                return response()->json(["response" => ["code" => "2", "success" => "-1",
                  "message" => "Invalid login credentials."]]);
            }
    
            $accessToken = Auth::user()->createToken('authToken')->accessToken;
    
            return response()->json(["response" => ["code" => "1", "success" => "1", "message" => "User registered."], 
            "user" => Auth::user(), 'accessToken' => $accessToken]);
        }
    }
}
