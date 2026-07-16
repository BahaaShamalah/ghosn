<?php

namespace App\Providers;

use App\Listeners\MarkEmailLogSent;
use App\Models\ContactMessage;
use App\Models\VolunteerApplication;
use App\Services\Settings\SettingsService;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\Settings\SettingsService::class);
        $this->app->singleton(\App\Support\GoogleSettings::class);
        $this->app->singleton(\App\Services\Google\GoogleIntegrationService::class);
        $this->app->singleton(\App\Services\Google\RecaptchaVerifier::class);
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Event::listen(MessageSent::class, MarkEmailLogSent::class);
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        config([
            'app.name' => (string) $settings->get('site.name_en', config('app.name')),
        ]);

        View::composer(['admin.partials.topbar', 'admin.layouts.app'], function ($view): void {
            $view->with('adminBreadcrumbs', \App\Support\AdminBreadcrumbs::forCurrentRoute());
        });

        View::composer(['admin.partials.sidebar', 'admin.partials.topbar'], function ($view): void {
            $user = auth()->user();
            $badges = [];

            if ($user?->hasPermission('messages.manage')) {
                $badges['messages'] = ContactMessage::query()->unread()->count();
            }

            if ($user?->hasPermission('volunteers.manage')) {
                $badges['volunteers'] = VolunteerApplication::query()->pending()->count();
            }

            $view->with('adminNavBadges', $badges);
        });

        View::composer('admin.partials.sidebar', function ($view): void {
            $view->with('adminNav', \App\Support\AdminNav::visibleFor(auth()->user()));
        });

        View::composer(['admin.partials.topbar'], function ($view): void {
            $user = auth()->user();
            $view->with('adminInboxCount', $user?->hasPermission('messages.manage')
                ? ContactMessage::query()->unread()->count()
                : 0);
            $view->with('adminInboxUrl', $user?->hasPermission('messages.manage')
                ? route('admin.messages.index')
                : null);
        });

        View::composer(['admin.layouts.app'], function ($view): void {
            $view->with('themeFonts', \App\Support\ThemeHelper::fonts());
        });
    }
}
