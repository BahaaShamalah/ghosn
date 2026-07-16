<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\LocaleHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminLocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse|JsonResponse
    {
        if (! LocaleHelper::isSupported($locale)) {
            abort(404);
        }

        session(['admin_locale' => $locale]);

        if ($request->expectsJson()) {
            return response()->json([
                'locale' => $locale,
                'dir' => LocaleHelper::direction($locale),
            ]);
        }

        $fallback = auth()->check() ? route('admin.dashboard') : route('admin.login');

        return redirect()->back(fallback: $fallback);
    }
}
