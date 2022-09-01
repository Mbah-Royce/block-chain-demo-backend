<?php

namespace App\Http\Controllers;

use App\Events\NewBlock;
use App\Events\NewTransaction;
use App\Http\Requests\TransactionRequest;
use App\Models\Block;
use App\Models\Coordinate;
use App\Models\LandCertificate;
use App\Models\Partitions;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function create(TransactionRequest $transactionRequest)
    {
        
        $data = [$transactionRequest->partitionId];
        $message = "Transaction Successful";
        $statusCode = 201;
        // return apiResponse($data,$message,$statusCode);
        $userSender = User::where("public_key", $transactionRequest->sender)->first();
        $userReciever = User::where("public_key", $transactionRequest->reciever)->first();

        if ($transactionRequest->type == 'whole-land') {
            $landCetificate = LandCertificate::where(['serial_no' => $transactionRequest->serial_no, 'user_id' => auth()->user()->id])->first();
            if (!$landCetificate) {
                $data = [];
                $message = "Certificate not foound";
                $statusCode = 404;
                return apiResponse($data, $message, $statusCode);
            }
            if (!$landCetificate->partitioned) {
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
                $this->newTrans($userSender->name, $userReciever->name, $transaction->area);
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
            } else {
                $message = "Transaction Unsucccessful land already has a portion";
                $statusCode = 422;
            }
        } else if ($transactionRequest->type == 'whole-partition') {
            $partition = Partitions::where(['user_id' => auth()->user()->id, 'id' => $transactionRequest->partitionId])->first();
            $partition->update([
                'user_id' => $userReciever->id
            ]);
            $landCetificate = $partition->certificate;
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
            $this->newTrans($userSender->name, $userReciever->name, $transaction->area);
            $data['block']  = createBlock(
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
            $message = "transaction type not valid";
            $statusCode = 422;
        }

        return apiResponse($data, $message, $statusCode);
    }

    public function createBlock($transId, $transAmt, $transReciver, $transSender, $transSignature, $userSender, $userReciever)
    {
        $nonce = null;
        $hash = null;
        $block = Block::orderBy('id', 'desc')->first();
        if ($block) {
            $prevHash = $block->hash;
        } else {
            $prevHash = "0000000000000000000000000000000000000000000000000000000000";
        }
        $prevBlockId = $block->id + 1;
        for ($i = 0; $i < 500000; ++$i) {
            $hash = hash(
                'sha256',
                $prevHash .
                    $prevBlockId .
                    $i .
                    $transId .
                    $transAmt .
                    $transReciver .
                    $transSender .
                    $transSignature,
                false
            );
            if (substr($hash, 0, 4) === "0000") {
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

    public function newTrans($senderName, $reciverName, $area)
    {
        $transaction = [
            "sender" => $senderName,
            "reciever" => $reciverName,
            "area" => $area
        ];
        NewTransaction::dispatch($transaction);
    }
}
