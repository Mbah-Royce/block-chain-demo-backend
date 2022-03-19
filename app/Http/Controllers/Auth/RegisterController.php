<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Registers new users
     * 
     * @param string name
     * @param string email
     * @return array response
     */
    public function register(RegisterRequest $registerRequest){

        $user = User::create([
            'name' => $registerRequest->name,
            'email' => $registerRequest->email,
            'public_key' => $registerRequest->publicKey,
            'private_key' => $registerRequest->privateKey
        ]);
        $token = $user->createToken('auth-token')->plainTextToken;
        $data = [
            'publicKey' => $registerRequest->publicKey,
            'privateKey' => $registerRequest->privateKey,
            'token' => $token,
            'area' => $user->area,
            'id' => $user->id
        ];
        $message = "Wallet Created Successfully";
        $statusCode = 201;
        return apiResponse($data,$message,$statusCode);
    }
}
