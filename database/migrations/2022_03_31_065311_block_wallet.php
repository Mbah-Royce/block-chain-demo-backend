<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BlockWallet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block_wallet',function(Blueprint $table){
            $table->unsignedBigInteger('block_id');
            $table->unsignedBigInteger('wallet_id');
            $table->unsignedBigInteger('transaction_id');
            $table->foreign('wallet_id')->references('id')->on('wallets');
            $table->foreign('block_id')->references('id')->on('blocks');
            $table->foreign('transaction_id')->references('id')->on('transactions');
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('block_walllet');
    }
}
