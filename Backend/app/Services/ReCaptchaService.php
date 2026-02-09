<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class ReCaptchaService
{
    public function verify(string $token, ?string $ip = null): void
    {
        $response = Http::withoutVerifying()
            ->asForm()
            ->post(config('recaptcha.verify_url'), [
                'secret'   => config('recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => $ip,
            ]);

        if (!$response->json('success')) {
            throw ValidationException::withMessages([
                'captcha_token' => 'La verificación del Captcha ha fallado. Inténtalo de nuevo.',
            ]);
        }
    }
}
