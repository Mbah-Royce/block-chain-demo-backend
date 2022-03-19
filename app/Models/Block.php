<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    protected $fillable = [
        "nonce",
        "hash",
        "preious_hash",
        "transaction_id"
    ];

    /**
     * Get all of the transactions for the Block
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
