<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $fillable = [
        "reciever",
        "sender",
        "area",
        "certificate_id",
        "serial_no",
        "partition_id",
        "signature",
        "amount",
        "type"
    ];

    /**
     * Get the block that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function block()
    {
        return $this->hasOne(Block::class);
    }
}
