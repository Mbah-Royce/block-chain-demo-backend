<?php

namespace App\Http\Controllers;

use App\Events\NewBlock;
use App\Http\Requests\TransactionRequest;
use App\Models\Block;
use App\Models\LandCertificate;
use App\Models\Partitions;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function create(TransactionRequest $transactionRequest){
        $data = [];
        $message = "Transaction Successful";
        $statusCode = 201;
        $userSender = User::where("public_key",$transactionRequest->sender)->first();
        $userReciever = User::where("public_key",$transactionRequest->reciever)->first();
        $landCetificate = LandCertificate::where(['serial_no' => $transactionRequest->serial_no,'user_id' => $userSender->id])->first();
        if(!$landCetificate){
            $data = [];
            $message = "Certificate not foound";
            $statusCode = 404;
            return apiResponse($data,$message,$statusCode); 
        }
        if($transactionRequest->type == 'whole-land'){
            if($landCetificate->partition->isEmpty()){
                $landCetificate->update([
                    'owner_name' => $userReciever->name,
                    'user_id' => $userReciever->id 
                ]);
                $transaction = Transaction::create([
                    "reciever" => $transactionRequest->reciever,
                    "sender" => $transactionRequest->sender,
                    "area" => $transactionRequest->area,
                    "certificate_id" => $landCetificate->id,
                    "serial_no" => $transactionRequest->serial_no,
                    "signature" => $transactionRequest->signature,
                    "area" => $landCetificate->area,
                    'type' => $transactionRequest->type
                ]);
                $data['block'] = $this->createBlock(
                    $transaction->id,
                    $transaction->area,
                    $transaction->reciever,
                    $transaction->sender,
                    $transaction->signature,
                    $userSender,
                    $userReciever
                );
                $data['transaction'] = $transaction;
            }else{
                $message = "Transaction Unsucccessful land already has a portion";
                $statusCode = 422; 
            }
        }else if($transactionRequest->type == 'portion-title'){
            if(!$transactionRequest->area || $transactionRequest->area > $landCetificate->area){
                $data = [];
                $message = "Error in area";
                $statusCode = 422;
            return apiResponse($data,$message,$statusCode); 
            }
            $partition = $landCetificate->partition()->create([
                'area' => $transactionRequest->area,
                'location' => $landCetificate->location,
                'user_id' => $userReciever->id,
            ]);
            $transaction = Transaction::create([
                "reciever" => $transactionRequest->reciever,
                "sender" => $transactionRequest->sender,
                "area" => $transactionRequest->area,
                "certificate_id" => $landCetificate->id,
                "serial_no" => $transactionRequest->serial_no,
                "partition_id" => $partition->id,
                "signature" => $transactionRequest->signature,
                'type' => $transactionRequest->type
            ]);
            $data['block']  = $this->createBlock(
            $transaction->id,
            $transaction->area,
            $transaction->reciever,
            $transaction->sender,
            $transaction->signature,
            $userSender,
            $userReciever);
            $data['transaction'] = $transaction;
        } else if($transactionRequest->type == 'portion-portion'){
            $partition = Partitions::find($transactionRequest->partitionId);
            if($partition->user_id != $userSender->id  || $transactionRequest->area > $partition->area){
                $data = [];
                $message = "Error in area";
                $statusCode = 422;
            return apiResponse($data,$message,$statusCode); 
            }
            $landCetificate = LandCertificate::find($partition->land_certificate_id);
            $partition = $landCetificate->partition()->create([
                'area' => $transactionRequest->area,
                'location' => $landCetificate->location,
                'user_id' => $userReciever->id,
            ]);
            $transaction = Transaction::create([
                "reciever" => $transactionRequest->reciever,
                "sender" => $transactionRequest->sender,
                "area" => $transactionRequest->area,
                "certificate_id" => $landCetificate->id,
                "serial_no" => $transactionRequest->serial_no,
                "partition_id" => $partition->id,
                "signature" => $transactionRequest->signature,
                'type' => $transactionRequest->type
            ]);
            $data['block']  = $this->createBlock(
            $transaction->id,
            $transaction->area,
            $transaction->reciever,
            $transaction->sender,
            $transaction->signature,
            $userSender,
            $userReciever);
            $data['transaction'] = $transaction;
        }

        return apiResponse($data,$message,$statusCode);
    }

    public function createBlock($transId,$transAmt,$transReciver,$transSender,$transSignature,$userSender,$userReciever)
    {
        $nonce = null;
        $hash = null;
        $block = Block::orderBy('id', 'desc')->first();
        if($block){
            $prevHash = $block->hash;
        }else{
            $prevHash = "0000000000000000000000000000000000000000000000000000000000";
        }
        $prevBlockId = $block->id + 1;
        for ( $i = 0; $i < 500000; ++$i ){
            $hash = hash('sha256',
            $prevHash.
            $prevBlockId.
            $i.
            $transId.
            $transAmt.
            $transReciver.
            $transSender.
            $transSignature,false);
            if(substr($hash,0,4) === "0000"){
                $nonce = $i;
                break;
            }
        }
        $block = Block::create([
            'transaction_id' => $transId,
            'preious_hash' => $prevHash,
            'nonce' => $nonce,
            'hash' => $hash
        ]);
        $newBlock = [
            'block_id' => $block->id,
            'block_nonce' => $block->nonce,
            'block_hash' => $block->hash,
            'block_prev_hash' => $block->preious_hash,
            'transaction_id' => $block->transaction->id,
            'transaction_amount' => $block->transaction->area,
            'transactiono_reciver' => $block->transaction->reciever,
            'transaction_sender' => $block->transaction->sender,
            'transaction_signature' => $block->transaction->signature
        ];
        NewBlock::dispatch($newBlock);
        $senderWalletId = $userSender->wallet->id;
        $recieverWalletId = $userReciever->wallet->id;
        $block->wallet()->attach($senderWalletId, ['transaction_id' => $transId]);
        $block->wallet()->attach($recieverWalletId, ['transaction_id' => $transId]);
    }

}
