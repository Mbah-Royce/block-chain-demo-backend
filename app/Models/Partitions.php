<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partitions extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'area',
        'location',
        'user_id',
        'land_certificate_id',
        'feature_type',
        'coordinate_lenth',
        'feature_id'
    ];

    /**
     * Get the user that owns the Partitions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user that owns the Partitions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function certificate()
    {
        return $this->belongsTo(LandCertificate::class);
    }

    /**
     * Get the coordinate that owns the LandCertificate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coordinate()
    {
        return $this->hasMany(PartitionCordinate::class,'partition_id','id');
    }
}
