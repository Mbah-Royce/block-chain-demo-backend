<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
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
            'id' => $user->id,
            'name' => $user->name
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

    public function getPortions(){
        $user = Auth::user();
        $data = $user->partition;
        $statusCode = 200;
        $message = "user portions";
        return apiResponse($data,$message,$statusCode); 
    }

    public function getCertificates(){
        $user = Auth::user();
        $data = $user->landCertificate;
        $statusCode = 200;
        $message = "user certificates";
        return apiResponse($data,$message,$statusCode); 
    }

    public function getTransStarts(){
        $user = Auth::user();
        $Certificates = $user->landCertificate;
        $numCertificates = $Certificates->count();
        $CertificatesLandSum = $Certificates->sum('area');
        $certPartitionArea = 0;
        foreach($Certificates as $Certificate){
            if(!$Certificate->partition->isEmpty()){
                $certPArts = $Certificate->partition;
                foreach($certPArts as $part){
                    $certPartitionArea = $certPartitionArea + $part->sum('area');
                }
            }
        }
        $CertificatesLandSum = $Certificates->sum('area') - $certPartitionArea; 
        $partition = $user->partition;
        $numpartition = $partition->count();
        $sumArea = $partition->sum('area') + $CertificatesLandSum;
        $data = [
            'total_share_cert' => $numpartition,
            'total_own_cert' => $numCertificates,
            'total_area' => $sumArea
        ];
        $statusCode = 200;
        $message = "user trans stats";
        return apiResponse($data,$message,$statusCode); 
    }

    public function getLastTrans(){
        $user = Auth::user();
        $data = Transaction::where(['reciever' => $user->public_key])->orwhere(['sender' => $user->public_key])->get()
        ->map(function($trans){
            $reciver = User::where(['public_key' => $trans->reciever])->first();
            $sender = User::where(['public_key' => $trans->sender])->first();
            return [
                'id' => $trans->id,
                'reciever' => $reciver->name,
                'sender' => $sender->name,
                'area' => $trans->area,
                'created_at' => $trans->created_at
            ];
        });
        $statusCode = 200;
        $message = "user trans latest";
        return apiResponse($data,$message,$statusCode); 
    }
}
