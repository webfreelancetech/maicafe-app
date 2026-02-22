<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class OtpVerification extends Model
{
    protected $fillable = ['email', 'otp', 'type', 'payload', 'expires_at'];

    protected $casts = [
        'payload'    => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * Generate a new 6-digit OTP, replace any existing one for this email+type.
     *
     * @param string      $email
     * @param string      $type    registration|forgot_password
     * @param array|null  $payload Extra data to persist (e.g. pending registration fields)
     * @return array ['model' => OtpVerification, 'plain_otp' => string]
     */
    public static function generate(string $email, string $type, ?array $payload = null): array
    {
        // Remove any existing record for this email + type
        static::where('email', $email)->where('type', $type)->delete();

        $plainOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $model = static::create([
            'email'      => $email,
            'otp'        => Hash::make($plainOtp),
            'type'       => $type,
            'payload'    => $payload,
            'expires_at' => now()->addMinutes(10),
        ]);

        return ['model' => $model, 'plain_otp' => $plainOtp];
    }

    /**
     * Find a valid (not expired) OTP record.
     */
    public static function findValid(string $email, string $type): ?self
    {
        return static::where('email', $email)
            ->where('type', $type)
            ->where('expires_at', '>', now())
            ->first();
    }

    /**
     * Check if this record is expired.
     */
    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    /**
     * Verify a plain OTP against the stored hash.
     */
    public function verify(string $plainOtp): bool
    {
        return Hash::check($plainOtp, $this->otp);
    }
}
