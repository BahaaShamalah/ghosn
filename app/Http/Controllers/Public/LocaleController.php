<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\LocaleHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse|JsonResponse
    {
        if (! LocaleHelper::isSupported($locale)) {
            abort(404);
        }

        session(['locale' => $locale]);

        if ($request->expectsJson()) {
            return response()->json([
                'locale' => $locale,
                'dir' => LocaleHelper::direction($locale),
            ]);
        }

        return redirect()->back(fallback: route('home'));
    }
}
