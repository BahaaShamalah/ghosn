<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\VolunteerApplication;
use App\Services\Google\RecaptchaVerifier;
use App\Support\GoogleSettings;
use App\Services\Volunteers\VolunteerEmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VolunteerApplicationController extends Controller
{
    public function __construct(
        private readonly VolunteerEmailService $emails,
        private readonly RecaptchaVerifier $recaptcha,
        private readonly GoogleSettings $google,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'area' => ['required', 'string', 'max:80'],
            'message' => ['nullable', 'string', 'max:2000'],
            'g-recaptcha-response' => ['nullable', 'string'],
        ];

        if ($this->google->recaptchaEnabled()) {
            $rules['g-recaptcha-response'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);

        if ($this->google->recaptchaEnabled()) {
            $ok = $this->recaptcha->verify(
                $validated['g-recaptcha-response'] ?? null,
                'volunteer',
                $request->ip(),
            );

            if (! $ok) {
                return response()->json([
                    'ok' => false,
                    'message' => __('public.contact.recaptcha_failed'),
                ], 422);
            }
        }

        unset($validated['g-recaptcha-response']);

        $application = VolunteerApplication::query()->create([
            ...$validated,
            'status' => VolunteerApplication::STATUS_PENDING,
            'locale' => app()->getLocale(),
            'ip_address' => $request->ip(),
        ]);

        $this->emails->afterApplicationSubmitted($application);

        return response()->json(['ok' => true]);
    }
}
