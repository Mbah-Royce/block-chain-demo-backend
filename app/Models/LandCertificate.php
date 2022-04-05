<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "location",
        "area",
        "owner_name",
        "serial_no"
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
}
