<?php

namespace App\Services;

use App\Models\EmailVerificationCode;
use App\Mail\EmailVerificationCodeMail;
use Illuminate\Support\Facades\Mail;

class EmailVerificationService
{
    /**
     * Send verification code to email
     */
    public static function sendCode(string $email, string $type = 'registration'): EmailVerificationCode
    {
        $verificationCode = EmailVerificationCode::createForEmail($email, $type);
        
        Mail::to($email)->send(new EmailVerificationCodeMail($verificationCode->code, $type));
        
        return $verificationCode;
    }

    /**
     * Verify the code
     */
    public static function verify(string $email, string $code, string $type = 'registration'): bool
    {
        return EmailVerificationCode::verify($email, $code, $type);
    }
}
