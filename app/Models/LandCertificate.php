<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "feature_id",
        "feature_type",
        "area",
        "owner_name",
        "serial_no",
        "partitioned"
    ];

    /**
     * Get the user that owns the LandCertificate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the partitions for the LandCertificate
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function partition()
    {
        return $this->hasMany(Partitions::class);
    }

    /**
     * Get the coordinate that owns the LandCertificate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coordinate()
    {
        return $this->hasMany(Coordinate::class);
    }
}
