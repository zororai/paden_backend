<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmailVerificationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'code',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Generate a new 6-digit verification code
     */
    public static function generateCode(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new verification code for an email
     */
    public static function createForEmail(string $email): self
    {
        // Delete any existing codes for this email
        self::where('email', $email)->delete();

        // Create new code that expires in 3 minutes
        return self::create([
            'email' => $email,
            'code' => self::generateCode(),
            'expires_at' => Carbon::now()->addMinutes(3),
        ]);
    }

    /**
     * Check if the code is expired
     */
    public function isExpired(): bool
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }

    /**
     * Verify a code for an email
     */
    public static function verify(string $email, string $code): bool
    {
        $record = self::where('email', $email)
            ->where('code', $code)
            ->first();

        if (!$record) {
            return false;
        }

        if ($record->isExpired()) {
            $record->delete();
            return false;
        }

        // Code is valid, delete it
        $record->delete();
        return true;
    }
}
