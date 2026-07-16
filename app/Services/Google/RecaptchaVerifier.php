<?php

namespace App\Services\Google;

use App\Support\GoogleSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaVerifier
{
    public function __construct(
        private readonly GoogleSettings $google,
    ) {}

    public function verify(?string $token, ?string $action = null, ?string $remoteIp = null): bool
    {
        if (! $this->google->recaptchaEnabled()) {
            return true;
        }

        $token = trim((string) $token);

        if ($token === '') {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(8)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $this->google->recaptchaSecretKey(),
                'response' => $token,
                'remoteip' => $remoteIp,
            ]);
        } catch (\Throwable $e) {
            Log::warning('reCAPTCHA verification request failed', ['message' => $e->getMessage()]);

            return false;
        }

        if (! $response->successful()) {
            return false;
        }

        $payload = $response->json();

        if (! ($payload['success'] ?? false)) {
            return false;
        }

        $score = (float) ($payload['score'] ?? 0);

        if ($score < $this->google->recaptchaScoreThreshold()) {
            return false;
        }

        if ($action !== null && isset($payload['action']) && $payload['action'] !== $action) {
            return false;
        }

        return true;
    }
}
