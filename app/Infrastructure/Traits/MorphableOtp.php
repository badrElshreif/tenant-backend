<?php

namespace App\Infrastructure\Traits;


use App\User\Domain\Models\Otp;

trait MorphableOtp
{
    public function generateOtp($method = null): string
    {
        $code = rand(100000, 999999);
        Otp::query()->updateOrCreate([
            'otpable_type' => static::class,
            'otpable_id' => $this->id,
            'method' => $method ?? null,
        ], [
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
        ]);
        return $code;
    }

    public function sendOtp(string $otp): bool
    {
        // Implement logic to send OTP via SMS, email, etc. (replace with your chosen method)
        // You can use Laravel notifications or external libraries for sending OTPs.
        return true; // Replace with actual sending logic and return success/failure
    }

    public function getOtp($code, $method = null)
    {
        $row = Otp::where('otpable_id', $this->id)
            ->where('otpable_type', static::class)->where('code', $code);
        if ($method) {
            $row->where('method', $method);
        }
        return $row->latest()->first();
    }

    public function clearOtp($code, $method = null): bool
    {
        $row = $this->getOtp($code, $method);
        if ($row) {
            $row->delete();
            return true;
        }
        return false;
    }

    public function hasVerifiedOtp(): bool
    {
        // TODO: Implement hasVerifiedOtp() method.
    }

    public function verifyOtp(string $otp): bool
    {
        // TODO: Implement verifyOtp() method.
    }


}
