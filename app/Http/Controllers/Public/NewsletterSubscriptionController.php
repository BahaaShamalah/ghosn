<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Services\Settings\SettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterSubscriptionController extends Controller
{
    public function store(Request $request, SettingsService $settings): JsonResponse
    {
        if (! (bool) $settings->get('newsletter.enabled', true)) {
            return response()->json(['message' => 'Newsletter subscriptions are disabled.'], 403);
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:190'],
        ]);

        NewsletterSubscriber::query()->firstOrCreate(
            ['email' => strtolower($validated['email'])],
            [
                'locale' => app()->getLocale(),
                'ip_address' => $request->ip(),
            ],
        );

        return response()->json(['ok' => true]);
    }
}
