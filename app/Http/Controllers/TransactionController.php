<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function create(TransactionRequest $transactionRequest){
        $userSender = User::where("public_key",$transactionRequest->sender)->first();
        if($userSender->area > $transactionRequest->amount){
            $data = [];
            $statusCode = 422;
            $message = "Amount invalid";
            return apiResponse($data,$message,$statusCode);
        }
        $transaction = Transaction::create([
            "amount" => $transactionRequest->amount,
            "reciever" => $transactionRequest->reciever,
            "sender" => $transactionRequest->sender,
            "signature" => $transactionRequest->signature,
        ]);
        $userReciver = User::where("public_key",$transactionRequest->reciever)->first();
        $userReciver->update(["area" => $transactionRequest->amount + $userReciver->area]);
        $userSender->update(["area" => $userSender->area - $transactionRequest->amount]);
        $nonce = null;
        $hash = null;
        for ( $i = 0; $i < 500000; ++$i ){
            $hash = hash('sha256',
            $i.
            $transaction->id.
            $transaction->amount.
            $transaction->reciever.
            $transaction->sender.
            $transaction->signature,false);
            if(substr($hash,0,4) === "0000"){
                $nonce = $i;
                break;
            }
        }
        $prevHash = null;
        $block = Block::orderBy('id','desc')->first();
        if($block == null){
            $prevHash = "0000000000000000000000000000000000000000000000000000000000";
        }else{
            $prevHash = $block->hash;
        }
        $block = $transaction->block()->create([
            'preious_hash' => $prevHash,
            'nonce' => $nonce,
            'hash' => $hash
        ]);
        $data = [
            'transaction_id' => $transaction->id,
            'prevHash' => $prevHash,
            'nonce' => $nonce,
            'hash' => $hash
        ];
        $statusCode = 201;
        $message = "Trasaction Created Successfully";
        return apiResponse($data,$message,$statusCode);
    }
}
