<?php

namespace App\Http\Controllers;

use App\Events\NewBlock;
use App\Models\Block;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function index(){
        $blocks = Block::all()->map(function ($block){
            return [
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
        });
        $message = "Block Listing";
        $statusCode = 200;
        $data =  $blocks;
        return apiResponse($data,$message,$statusCode);
    }

    public function fireEvents(){
        NewBlock::dispatch("dfdfdfd");
    }
}
