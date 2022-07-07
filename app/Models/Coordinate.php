<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordinate extends Model
{
    use HasFactory;

    protected $fillable = [
        'lat',
        'lng'
    ];
    protected $table = 'coordinate';

    /**
     * Get the feature that owns the Coordinate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feature()
    {
        return $this->belongsTo(LandCertificate::class);
    }
}
