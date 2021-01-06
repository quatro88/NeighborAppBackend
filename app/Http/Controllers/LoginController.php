<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request){
        $login = $request->validate([
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if(!Auth::attempt($login)){
            return response()->json(["response" => ["code" => "2", "success" => "-1",
                  "message" => "Invalid login credentials."]]);
        }

        $accessToken = Auth::user()->createToken('authToken')->accessToken;

        return response()->json(["response" => ["code" => "1", "success" => "1", "message" => "User logged in."], 
            "user" => Auth::user(), 'accessToken' => $accessToken]);
    }
}
