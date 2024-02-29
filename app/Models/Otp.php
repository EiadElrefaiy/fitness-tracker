<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $table = 'otp';
    protected $fillable = [
        'user_id',
        'otp',
        'expires_at',
    ];
    protected $dates = ['expires_at'];

    // Accessor to format expires_at as Carbon instance
    public function getExpiresAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value);
    }

}
