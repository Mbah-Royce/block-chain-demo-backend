<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(){
        $user = Auth::user();
        $statusCode = 200;
        $data = [
            'publicKey' => $user->public_key,
            'privateKey' => $user->private_key,
            'area' => $user->area,
            'id' => $user->id
        ];
        $message = "user info";
        return apiResponse($data,$message,$statusCode);
    }

    public function allUsers(){
        $user = User::all();
        $statusCode = 200;
        $data = $user;
        $message = "user info";
        return apiResponse($data,$message,$statusCode); 
    }
}
