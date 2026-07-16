<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\UpdateSettingsGroupRequest;
use App\Models\Media;
use App\Models\Page;
use App\Models\SocialLink;
use App\Services\Settings\SettingsService;
use App\Support\PaymentSettings;
use App\Support\SettingsHub;
use App\Support\SettingsInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        private readonly SettingsService $settings,
        private readonly PaymentSettings $payments,
    ) {}

    public function index(): View
    {
        return view('admin.settings.hub', [
            'cards' => SettingsHub::cards(),
        ]);
    }

    public function show(string $group): View|RedirectResponse
    {
        if (! SettingsHub::exists($group)) {
            abort(404);
        }

        if ($group === 'about') {
            $page = Page::query()->where('slug', 'who-we-are')->first();

            if ($page) {
                return redirect()->route('admin.pages.show', $page);
            }
        }

        if ($group === 'contact') {
            $page = Page::query()->where('slug', 'contact')->first();

            if ($page) {
                return redirect()->route('admin.pages.show', $page);
            }
        }

        if (! view()->exists('admin.settings.groups.'.$group)) {
            abort(404);
        }

        return view('admin.settings.show', [
            'group' => $group,
            'settings' => $this->settings->all(),
            'mediaLibrary' => Media::query()->latest()->limit(100)->get(),
            'homePage' => Page::query()->where('slug', 'home')->withCount('sections')->first(),
            'fontOptions' => ['Montserrat', 'Cairo', 'Inter', 'Roboto', 'Open Sans', 'Lato', 'Poppins', 'Noto Sans Arabic'],
            'paymentEnv' => $group === 'payments' ? [
                'stripe_configured' => $this->payments->stripeEnvConfigured(),
                'paypal_configured' => $this->payments->paypalEnvConfigured(),
                'paypal_webhook_configured' => $this->payments->paypalWebhookConfigured(),
            ] : null,
            'socialLinks' => $group === 'social' ? SocialLink::query()->ordered()->get() : null,
            'socialPlatforms' => $group === 'social' ? \App\Support\SocialPlatform::definitions() : null,
        ]);
    }

    public function updateGroup(UpdateSettingsGroupRequest $request, string $group): RedirectResponse
    {
        if (! SettingsHub::exists($group) || ! SettingsHub::isEditable($group)) {
            abort(404);
        }

        if (! in_array($group, config('settings.allowed_groups', []), true)) {
            abort(404);
        }

        $allowedKeys = SettingsInput::keysForGroup($group);
        $flat = SettingsInput::flatten($request->validated());

        $payload = [];

        foreach ($allowedKeys as $key) {
            if (array_key_exists($key, $flat)) {
                $payload[$key] = $flat[$key];

                continue;
            }

            $prefix = $key.'.';
            $nested = [];

            foreach ($flat as $flatKey => $value) {
                if (! str_starts_with((string) $flatKey, $prefix)) {
                    continue;
                }

                data_set($nested, substr((string) $flatKey, strlen($prefix)), $value);
            }

            if ($nested !== []) {
                $payload[$key] = $nested;
            }
        }

        if ($group === 'branding') {
            if (empty($payload['site.logo_media_id'])) {
                $payload['site.logo_media_id'] = null;
            }

            if (empty($payload['site.favicon_media_id'])) {
                $payload['site.favicon_media_id'] = null;
            }
        }

        if ($group === 'seo') {
            if (empty($payload['seo.image_media_id'])) {
                $payload['seo.image_media_id'] = null;
            }

            if (isset($payload['seo.robots_txt']) && is_array($payload['seo.robots_txt'])) {
                $payload['seo.robots_txt'] = \App\Support\RobotsTxtBuilder::normalize($payload['seo.robots_txt']);
            }

            \App\Support\SitemapBuilder::clearCache();
        }

        if ($group === 'google') {
            foreach ([
                'google.analytics.measurement_id',
                'google.gtm.container_id',
                'google.adsense.publisher_id',
            ] as $idKey) {
                if (isset($payload[$idKey]) && is_string($payload[$idKey])) {
                    $payload[$idKey] = strtoupper(trim($payload[$idKey]));
                    if ($idKey === 'google.adsense.publisher_id') {
                        $payload[$idKey] = strtolower(trim((string) $payload[$idKey]));
                    }
                }
            }
        }

        if ($group === 'navigation') {
            $items = collect($payload['navigation.items'] ?? [])
                ->filter(fn ($item): bool => is_array($item) && (trim((string) ($item['label_en'] ?? '')) !== '' || trim((string) ($item['label_ar'] ?? '')) !== ''))
                ->map(fn ($item): array => [
                    'label_en' => trim((string) ($item['label_en'] ?? '')),
                    'label_ar' => trim((string) ($item['label_ar'] ?? '')),
                    'href' => trim((string) ($item['href'] ?? '')),
                ])
                ->values()
                ->all();

            $payload['navigation.items'] = $items;
        }

        if ($group === 'footer') {
            $items = collect($payload['footer.links'] ?? [])
                ->filter(fn ($item): bool => is_array($item) && (trim((string) ($item['label_en'] ?? '')) !== '' || trim((string) ($item['label_ar'] ?? '')) !== ''))
                ->map(fn ($item): array => [
                    'label_en' => trim((string) ($item['label_en'] ?? '')),
                    'label_ar' => trim((string) ($item['label_ar'] ?? '')),
                    'href' => trim((string) ($item['href'] ?? '')),
                ])
                ->values()
                ->all();

            $payload['footer.links'] = $items;
        }

        if ($group === 'legal' && isset($payload['legal.pages']) && is_array($payload['legal.pages'])) {
            $payload['legal.pages'] = \App\Support\LegalPageInput::normalizePages($payload['legal.pages']);
        }

        if ($group === 'about' && isset($payload['about.page']) && is_array($payload['about.page'])) {
            $payload['about.page'] = \App\Support\AboutPageInput::normalize($payload['about.page']);
        }

        if ($group === 'contact' && isset($payload['contact.page']) && is_array($payload['contact.page'])) {
            $payload['contact.page'] = \App\Support\ContactPageInput::normalize($payload['contact.page']);
        }

        if ($payload !== []) {
            $this->settings->setMany($payload);
        }

        if ($group === 'branding') {
            if ($request->hasFile('site.logo')) {
                $path = $request->file('site.logo')->store('site', 'public');
                $this->settings->set('site.logo', 'storage/'.$path);
                $this->settings->set('site.logo_media_id', null);
            }

            if ($request->hasFile('site.favicon')) {
                $path = $request->file('site.favicon')->store('site', 'public');
                $this->settings->set('site.favicon', 'storage/'.$path);
                $this->settings->set('site.favicon_media_id', null);
            }
        }

        if ($group === 'seo' && $request->hasFile('seo.image')) {
            $path = $request->file('seo.image')->store('site', 'public');
            $this->settings->set('seo.image', 'storage/'.$path);
            $this->settings->set('seo.image_media_id', null);
        }

        if ($group === 'google' && $request->hasFile('google.search_console.verification_upload')) {
            $file = $request->file('google.search_console.verification_upload');
            $safeName = preg_replace('/[^A-Za-z0-9._-]/', '', $file->getClientOriginalName()) ?: 'google-site-verification.html';

            if (! str_ends_with(strtolower($safeName), '.html') && ! str_ends_with(strtolower($safeName), '.htm')) {
                $safeName .= '.html';
            }

            $file->move(public_path(), $safeName);
            $this->settings->set('google.search_console.verification_file', $safeName);
        }

        $this->settings->clearCache();

        if ($group === 'about') {
            $page = Page::query()->where('slug', 'who-we-are')->first();

            if ($page) {
                return redirect()
                    ->route('admin.pages.show', $page)
                    ->with('status', __('admin.settings.saved_group', ['group' => __("admin.settings.group_{$group}")]));
            }
        }

        if ($group === 'contact') {
            $page = Page::query()->where('slug', 'contact')->first();

            if ($page) {
                return redirect()
                    ->route('admin.pages.show', $page)
                    ->with('status', __('admin.settings.saved_group', ['group' => __("admin.settings.group_{$group}")]));
            }
        }

        return redirect()
            ->route('admin.settings.show', $group)
            ->with('status', __('admin.settings.saved_group', ['group' => __("admin.settings.group_{$group}")]));
    }
}
