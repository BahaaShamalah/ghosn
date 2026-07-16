<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\Contact\ContactEmailService;
use App\Services\Google\RecaptchaVerifier;
use App\Support\GoogleSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function __construct(
        private readonly ContactEmailService $emails,
        private readonly RecaptchaVerifier $recaptcha,
        private readonly GoogleSettings $google,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'subject' => ['nullable', 'string', 'max:190'],
            'message' => ['required', 'string', 'max:5000'],
            'g-recaptcha-response' => ['nullable', 'string'],
        ];

        if ($this->google->recaptchaForContact()) {
            $rules['g-recaptcha-response'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);

        if ($this->google->recaptchaForContact()) {
            $ok = $this->recaptcha->verify(
                $validated['g-recaptcha-response'] ?? null,
                'contact',
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

        $message = ContactMessage::query()->create([
            ...$validated,
            'subject' => $validated['subject'] ?? '',
            'is_read' => false,
            'locale' => app()->getLocale(),
            'ip_address' => $request->ip(),
        ]);

        $this->emails->afterMessageSubmitted($message);

        return response()->json(['ok' => true]);
    }
}
