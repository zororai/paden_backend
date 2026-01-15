<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VerificationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'code',
        'expires_at',
        'verified',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified' => 'boolean',
    ];

    public function isExpired()
    {
        return Carbon::now()->isAfter($this->expires_at);
    }

    public function isValid()
    {
        return !$this->verified && !$this->isExpired();
    }

    public static function generateCode()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function createForPhone($phone)
    {
        self::where('phone', $phone)
            ->where('verified', false)
            ->delete();

        return self::create([
            'phone' => $phone,
            'code' => self::generateCode(),
            'expires_at' => Carbon::now()->addMinutes(2),
            'verified' => false,
        ]);
    }
}
