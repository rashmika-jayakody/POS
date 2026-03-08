<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmailVerificationCode extends Model
{
    protected $fillable = [
        'email',
        'code',
        'expires_at',
        'used',
        'type',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    /**
     * Check if the code is valid (not used and not expired)
     */
    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture();
    }

    /**
     * Mark the code as used
     */
    public function markAsUsed(): void
    {
        $this->update(['used' => true]);
    }

    /**
     * Generate a 6-digit verification code
     */
    public static function generateCode(): string
    {
        return str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new verification code for an email
     */
    public static function createForEmail(string $email, string $type = 'registration'): self
    {
        // Invalidate any existing codes for this email
        self::where('email', $email)
            ->where('type', $type)
            ->where('used', false)
            ->update(['used' => true]);

        return self::create([
            'email' => $email,
            'code' => self::generateCode(),
            'expires_at' => Carbon::now()->addMinutes(15), // Code expires in 15 minutes
            'type' => $type,
        ]);
    }

    /**
     * Verify a code for an email
     */
    public static function verify(string $email, string $code, string $type = 'registration'): bool
    {
        $verificationCode = self::where('email', $email)
            ->where('code', $code)
            ->where('type', $type)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($verificationCode && $verificationCode->isValid()) {
            $verificationCode->markAsUsed();
            return true;
        }

        return false;
    }
}
