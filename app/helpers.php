<?php
use App\Models\Block;
use App\Events\NewBlock;
use App\Events\NewTransaction;
use App\Models\Transaction;

function apiResponse($data,$message = '',$statusCode = 200){
    $message = (is_array($message)) ? reset($message) : $message;
    $response['data'] = ($data) ?? [];
    $response['message'] = (is_array($message)) ? $message[0] : $message;
    return response()->json($response,$statusCode);
}

function createBlock($transId,$transAmt,$transReciver,$transSender,$transSignature,$userSender,$userReciever)
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
    // NewBlock::dispatch($newBlock);
    $senderWalletId = $userSender->wallet->id;
    $recieverWalletId = $userReciever->wallet->id;
    $block->wallet()->attach($senderWalletId, ['transaction_id' => $transId]);
    $block->wallet()->attach($recieverWalletId, ['transaction_id' => $transId]);
}

function createTransaction($reciever,$sender,$certificateId,$serialNo,$signature,$area,$type){
    $transaction = Transaction::create([
        "reciever" => $reciever,
        "sender" => $sender,
        "certificate_id" => $certificateId,
        "serial_no" => $serialNo,
        "signature" => $signature,
        "area" => $area,
        'type' => $type
    ]);
    return $transaction;
}

?>