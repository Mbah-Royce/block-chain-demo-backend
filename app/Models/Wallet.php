<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the user that owns the Wallet
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The blockAddress that belong to the Wallet
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function blockAddress()
    {
        return $this->belongsToMany(Block::class, 'block_wallet')->withPivot('transaction_id');
    }
}
