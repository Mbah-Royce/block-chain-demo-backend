<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Login function authenticate users
     * 
     * @param string publicKey
     * @param string privateKey
     * @return array response
     */
    public function login(LoginRequest $request){

        // if(Auth::attempt(['public_key' => $request->publicKey, 'private_key' => $request->privateKey])){
            $user =  User::where("public_key",$request->publicKey)->first();
            // $user = User::where("privateKey",$request->privateKey)->first();
            $token = $user->createToken('auth-token')->plainTextToken;
            $message = "Access Granted";
            $statusCode = 200;
            $data = [
                'publicKey' => $user->public_key,
                'privateKey' => $user->private_key,
                'token' => $token,
                'area' => $user->area,
                'id' => $user->id
            ];
        // }else{
        //     $message = "Access Denied";
        //     $statusCode = 401;
        //     $data = [];
        // }
        return apiResponse($data,$message,$statusCode);
    }
}
