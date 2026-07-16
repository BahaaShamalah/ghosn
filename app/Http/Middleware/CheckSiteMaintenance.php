<?php

namespace App\Http\Middleware;

use App\Support\MaintenanceSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSiteMaintenance
{
    public function __construct(
        private readonly MaintenanceSettings $maintenance,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldBypass($request)) {
            return $next($request);
        }

        if (! $this->maintenance->enabled()) {
            return $next($request);
        }

        $locale = app()->getLocale();
        $copy = $this->maintenance->copy($locale);

        return response()
            ->view('public.maintenance', [
                'title' => $copy['title'],
                'message' => $copy['message'],
                'eyebrow' => $copy['eyebrow'],
                'siteName' => $copy['siteName'],
                'logoUrl' => $copy['logoUrl'],
            ], Response::HTTP_SERVICE_UNAVAILABLE)
            ->header('Retry-After', '3600');
    }

    private function shouldBypass(Request $request): bool
    {
        if ($request->is('admin', 'admin/*')) {
            return true;
        }

        if ($request->is('up', 'webhooks/*')) {
            return true;
        }

        return false;
    }
}
