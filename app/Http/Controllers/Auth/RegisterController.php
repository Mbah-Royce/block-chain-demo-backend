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
            'public_key' => $registerRequest->public_key,
            'private_key' => $registerRequest->private_key
        ]);
        $user->wallet()->create();
        $token = $user->createToken('auth-token')->plainTextToken;
        $data = [
            'publicKey' => $registerRequest->public_key,
            'privateKey' => $registerRequest->private_key,
            'token' => $token,
            'area' => $user->area,
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role
        ];
        $message = "Wallet Created Successfully";
        $statusCode = 201;
        return apiResponse($data,$message,$statusCode);
    }
}
