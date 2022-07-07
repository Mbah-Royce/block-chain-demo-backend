<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartitionCordinate extends Model
{
    use HasFactory;

    protected $fillable = [
        'lat',
        'lng',
        'partition_id',
        'array_position'
    ];

    /**
     * Get the partition that owns the Coordinate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partition()
    {
        return $this->belongsTo(Partition::class);
    }
}
