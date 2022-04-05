<?php

namespace App\Http\Controllers;

use App\Events\NewBlock;
use App\Http\Requests\LandCertCreateRequest;
use App\Models\Block;
use App\Models\LandCertificate;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class LandCertificateController extends Controller
{
    public function create(LandCertCreateRequest $request)
    {
        $data = [];
        $message = "created successfully";
        $statusCdoe = 201;
        try {
            $user = User::where("public_key",$request->reciever)->first();
            $userS = User::where("public_key",$request->sender)->first();
            if($user){
                $landCetificate = $user->landCertificate()->create([
                    "location" => $request->location,
                    "area" => $request->area,
                    "owner_name" => $user->name,
                    "serial_no" => $this->generateRandomString().Carbon::now()->format('s')
                ]);
                $transaction = Transaction::create([
                    "reciever" => $request->reciever,
                    "sender" => $request->sender,
                    "area" => $request->area,
                    "certificate_id" => $landCetificate->id,
                    "serial_no" => $landCetificate->serial_no,
                    "signature" => $request->signature,
                    "area" => $landCetificate->area,
                    'type' => "whole-land"
                ]);
                $data['block'] = $this->createBlock(
                    $transaction->id,
                    $transaction->area,
                    $transaction->reciever,
                    $transaction->sender,
                    $transaction->signature,
                    $userS,
                    $user
                );
            }
        } catch (Exception $e) {
            $message = "Error : ".$e->getMessage();
            $statusCdoe = 500;
        } catch (ModelNotFoundException $e){
            $message = "Error : Model not found";
            $statusCdoe = 404;
        }

        return apiResponse($data,$message,$statusCdoe);
    }

    public function userCertificates()
    {
        $data = [];
        $message = "Listing successfully";
        $statusCdoe = 200;
        try {
            $user = User::find(auth()->id);
            $data = $user->landCertificate;
        } catch (Exception $e) {
            $message = "Error : ".$e->getMessage();
            $statusCdoe = 500;
        } catch (ModelNotFoundException $e){
            $message = "Error : Model Not Found";
            $statusCdoe = 404;
        }
        
        return apiResponse($data,$message,$statusCdoe);
    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function createBlock($transId,$transAmt,$transReciver,$transSender,$transSignature,$userSender,$userReciever)
    {
        $nonce = null;
        $hash = null;
        $block = Block::orderBy('id', 'desc')->first();
        if($block){
            $prevHash = $block->hash;
            $prevBlockId = $block->id + 1;
            
        }else{
            $prevHash = "0000000000000000000000000000000000000000000000000000000000";
            $prevBlockId = 1;
        }
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