<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('site.enable_animations') || $this->filled('site.enable_animations')) {
            $this->merge([
                'site' => array_merge($this->input('site', []), [
                    'enable_animations' => $this->boolean('site.enable_animations'),
                ]),
            ]);
        }

        if ($this->route('group') === 'donations') {
            $donations = $this->input('donations', []);

            $this->merge([
                'donations' => array_merge($donations, [
                    'enabled' => $this->boolean('donations.enabled'),
                    'bank_transfer_enabled' => $this->boolean('donations.bank_transfer_enabled'),
                ]),
            ]);
        }

        if ($this->route('group') === 'payments') {
            $payments = $this->input('payments', []);

            $this->merge([
                'payments' => array_merge($payments, [
                    'stripe_enabled' => $this->boolean('payments.stripe_enabled'),
                    'paypal_enabled' => $this->boolean('payments.paypal_enabled'),
                ]),
            ]);
        }

        if ($this->route('group') === 'email') {
            $email = $this->input('email', []);

            $this->merge([
                'email' => array_merge($email, [
                    'donor_receipts_enabled' => $this->boolean('email.donor_receipts_enabled'),
                    'admin_alerts_enabled' => $this->boolean('email.admin_alerts_enabled'),
                ]),
            ]);
        }

        if ($this->route('group') === 'newsletter') {
            $newsletter = $this->input('newsletter', []);

            $this->merge([
                'newsletter' => array_merge($newsletter, [
                    'enabled' => $this->boolean('newsletter.enabled'),
                ]),
            ]);
        }

        if ($this->route('group') === 'maintenance') {
            $maintenance = $this->input('maintenance', []);

            $this->merge([
                'maintenance' => array_merge($maintenance, [
                    'enabled' => $this->boolean('maintenance.enabled'),
                ]),
            ]);
        }

        if ($this->route('group') === 'volunteers') {
            $volunteers = $this->input('volunteers', []);
            $booleans = [];

            foreach (['confirmation', 'admin_alert', 'welcome', 'rejected'] as $type) {
                $booleans["{$type}_enabled"] = $this->boolean("volunteers.{$type}_enabled");
            }

            $this->merge([
                'volunteers' => array_merge($volunteers, $booleans),
            ]);
        }

        if ($this->route('group') === 'seo') {
            $seo = $this->input('seo', []);

            $this->merge([
                'seo' => array_merge($seo, [
                    'schema_organization' => $this->boolean('seo.schema_organization'),
                    'schema_website' => $this->boolean('seo.schema_website'),
                    'schema_article' => $this->boolean('seo.schema_article'),
                    'schema_breadcrumb' => $this->boolean('seo.schema_breadcrumb'),
                    'schema_faq' => $this->boolean('seo.schema_faq'),
                    'sitemap_enabled' => $this->boolean('seo.sitemap_enabled'),
                    'sitemap_include_pages' => $this->boolean('seo.sitemap_include_pages'),
                    'sitemap_include_posts' => $this->boolean('seo.sitemap_include_posts'),
                    'sitemap_include_campaigns' => $this->boolean('seo.sitemap_include_campaigns'),
                    'sitemap_include_categories' => $this->boolean('seo.sitemap_include_categories'),
                ]),
            ]);
        }

        if ($this->route('group') === 'google') {
            $google = $this->input('google', []);

            $this->merge([
                'google' => [
                    'analytics' => array_merge($google['analytics'] ?? [], [
                        'enabled' => $this->boolean('google.analytics.enabled'),
                        'anonymize_ip' => $this->boolean('google.analytics.anonymize_ip'),
                        'debug' => $this->boolean('google.analytics.debug'),
                        'enhanced_measurement' => $this->boolean('google.analytics.enhanced_measurement'),
                        'ecommerce' => $this->boolean('google.analytics.ecommerce'),
                        'measurement_id' => $google['analytics']['measurement_id'] ?? '',
                    ]),
                    'gtm' => array_merge($google['gtm'] ?? [], [
                        'enabled' => $this->boolean('google.gtm.enabled'),
                        'inject_head' => $this->boolean('google.gtm.inject_head'),
                        'inject_body' => $this->boolean('google.gtm.inject_body'),
                        'container_id' => $google['gtm']['container_id'] ?? '',
                    ]),
                    'search_console' => $google['search_console'] ?? [],
                    'consent' => array_merge($google['consent'] ?? [], [
                        'enabled' => $this->boolean('google.consent.enabled'),
                        'analytics_storage' => $google['consent']['analytics_storage'] ?? 'denied',
                        'ad_storage' => $google['consent']['ad_storage'] ?? 'denied',
                        'ad_user_data' => $google['consent']['ad_user_data'] ?? 'denied',
                        'ad_personalization' => $google['consent']['ad_personalization'] ?? 'denied',
                        'wait_for_update' => $google['consent']['wait_for_update'] ?? 500,
                        'regions' => $google['consent']['regions'] ?? '',
                        'cookie_days' => $google['consent']['cookie_days'] ?? 365,
                    ]),
                    'adsense' => array_merge($google['adsense'] ?? [], [
                        'enabled' => $this->boolean('google.adsense.enabled'),
                        'auto_ads' => $this->boolean('google.adsense.auto_ads'),
                        'publisher_id' => $google['adsense']['publisher_id'] ?? '',
                    ]),
                    'maps' => array_merge($google['maps'] ?? [], [
                        'enabled' => $this->boolean('google.maps.enabled'),
                        'api_key' => $google['maps']['api_key'] ?? '',
                        'language' => $google['maps']['language'] ?? '',
                        'region' => $google['maps']['region'] ?? '',
                    ]),
                    'recaptcha' => array_merge($google['recaptcha'] ?? [], [
                        'enabled' => $this->boolean('google.recaptcha.enabled'),
                        'contact' => $this->boolean('google.recaptcha.contact'),
                        'login' => $this->boolean('google.recaptcha.login'),
                        'register' => $this->boolean('google.recaptcha.register'),
                        'site_key' => $google['recaptcha']['site_key'] ?? '',
                        'secret_key' => $google['recaptcha']['secret_key'] ?? '',
                        'score_threshold' => $google['recaptcha']['score_threshold'] ?? '0.5',
                    ]),
                    'merchant' => $google['merchant'] ?? [],
                    'fonts' => array_merge($google['fonts'] ?? [], [
                        'enable_cdn' => $this->boolean('google.fonts.enable_cdn'),
                        'preconnect' => $this->boolean('google.fonts.preconnect'),
                        'display_swap' => $this->boolean('google.fonts.display_swap'),
                        'prefer_local' => $this->boolean('google.fonts.prefer_local'),
                        'family_en' => $google['fonts']['family_en'] ?? 'Montserrat',
                        'family_ar' => $google['fonts']['family_ar'] ?? 'Cairo',
                    ]),
                ],
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $group = (string) $this->route('group');

        return match ($group) {
            'general' => $this->generalRules(),
            'branding' => $this->brandingRules(),
            'typography' => $this->typographyRules(),
            'colors' => $this->colorRules(),
            'social' => $this->socialRules(),
            'contact' => $this->contactRules(),
            'navigation' => $this->navigationRules(),
            'footer' => $this->footerRules(),
            'donations' => $this->donationsRules(),
            'payments' => $this->paymentsRules(),
            'email' => $this->emailRules(),
            'newsletter' => $this->newsletterRules(),
            'volunteers' => $this->volunteersRules(),
            'legal' => $this->legalRules(),
            'team' => $this->teamRules(),
            'about' => $this->aboutRules(),
            'maintenance' => $this->maintenanceRules(),
            'seo' => $this->seoRules(),
            'google' => $this->googleRules(),
            'homepage', 'advanced' => [],
            default => abort(404),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function generalRules(): array
    {
        return [
            'site.name_en' => ['required', 'string', 'max:255'],
            'site.name_ar' => ['required', 'string', 'max:255'],
            'site.slogan_en' => ['nullable', 'string', 'max:500'],
            'site.slogan_ar' => ['nullable', 'string', 'max:500'],
            'site.default_language' => ['required', Rule::in(['en', 'ar'])],
            'site.enable_animations' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function brandingRules(): array
    {
        return [
            'site.logo_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'site.favicon_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'site.logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'site.favicon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,ico', 'max:1024'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function typographyRules(): array
    {
        return [
            'theme.font_en' => ['required', 'string', 'max:100'],
            'theme.font_ar' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function colorRules(): array
    {
        $hexColor = ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'];

        return [
            'theme.primary_color' => $hexColor,
            'theme.secondary_color' => $hexColor,
            'theme.accent_color' => $hexColor,
            'theme.background_color' => $hexColor,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function socialRules(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    private function contactRules(): array
    {
        return [
            'contact.email' => ['required', 'email', 'max:255'],
            'contact.inbox_email' => ['nullable', 'email', 'max:255'],
            'contact.phone' => ['nullable', 'string', 'max:60'],
            'contact.address_en' => ['nullable', 'string', 'max:255'],
            'contact.address_ar' => ['nullable', 'string', 'max:255'],
            'contact.office_en' => ['nullable', 'string', 'max:255'],
            'contact.office_ar' => ['nullable', 'string', 'max:255'],
            'contact.page_hero_eyebrow_en' => ['nullable', 'string', 'max:255'],
            'contact.page_hero_eyebrow_ar' => ['nullable', 'string', 'max:255'],
            'contact.page_hero_title_en' => ['nullable', 'string', 'max:255'],
            'contact.page_hero_title_ar' => ['nullable', 'string', 'max:255'],
            'contact.page_hero_subtitle_en' => ['nullable', 'string', 'max:1000'],
            'contact.page_hero_subtitle_ar' => ['nullable', 'string', 'max:1000'],
            'contact.page_info_eyebrow_en' => ['nullable', 'string', 'max:255'],
            'contact.page_info_eyebrow_ar' => ['nullable', 'string', 'max:255'],
            'contact.page_info_title_en' => ['nullable', 'string', 'max:255'],
            'contact.page_info_title_ar' => ['nullable', 'string', 'max:255'],
            'contact.page_info_body_en' => ['nullable', 'string', 'max:2000'],
            'contact.page_info_body_ar' => ['nullable', 'string', 'max:2000'],
            'contact.page' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function navigationRules(): array
    {
        return [
            'navigation.items' => ['nullable', 'array'],
            'navigation.items.*.label_en' => ['nullable', 'string', 'max:120'],
            'navigation.items.*.label_ar' => ['nullable', 'string', 'max:120'],
            'navigation.items.*.href' => ['nullable', 'string', 'max:255'],
            'navigation.donate_label_en' => ['required', 'string', 'max:60'],
            'navigation.donate_label_ar' => ['required', 'string', 'max:60'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function footerRules(): array
    {
        return [
            'footer.desc_en' => ['nullable', 'string', 'max:1000'],
            'footer.desc_ar' => ['nullable', 'string', 'max:1000'],
            'footer.tagline_en' => ['nullable', 'string', 'max:500'],
            'footer.tagline_ar' => ['nullable', 'string', 'max:500'],
            'footer.quick_title_en' => ['nullable', 'string', 'max:120'],
            'footer.quick_title_ar' => ['nullable', 'string', 'max:120'],
            'footer.links_title_en' => ['nullable', 'string', 'max:120'],
            'footer.links_title_ar' => ['nullable', 'string', 'max:120'],
            'footer.links' => ['nullable', 'array'],
            'footer.links.*.label_en' => ['nullable', 'string', 'max:120'],
            'footer.links.*.label_ar' => ['nullable', 'string', 'max:120'],
            'footer.links.*.href' => ['nullable', 'string', 'max:255'],
            'footer.contact_title_en' => ['nullable', 'string', 'max:120'],
            'footer.contact_title_ar' => ['nullable', 'string', 'max:120'],
            'footer.follow_title_en' => ['nullable', 'string', 'max:120'],
            'footer.follow_title_ar' => ['nullable', 'string', 'max:120'],
            'footer.rights_en' => ['nullable', 'string', 'max:255'],
            'footer.rights_ar' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function donationsRules(): array
    {
        return [
            'donations.enabled' => ['sometimes', 'boolean'],
            'donations.bank_transfer_enabled' => ['sometimes', 'boolean'],
            'donations.bank_name_en' => ['nullable', 'string', 'max:255'],
            'donations.bank_name_ar' => ['nullable', 'string', 'max:255'],
            'donations.account_holder_en' => ['nullable', 'string', 'max:255'],
            'donations.account_holder_ar' => ['nullable', 'string', 'max:255'],
            'donations.iban' => ['nullable', 'string', 'max:64'],
            'donations.account_number' => ['nullable', 'string', 'max:64'],
            'donations.swift' => ['nullable', 'string', 'max:32'],
            'donations.instructions_en' => ['nullable', 'string', 'max:1000'],
            'donations.instructions_ar' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function paymentsRules(): array
    {
        return [
            'payments.currency' => ['required', Rule::in(array_keys(config('donations.currencies', [])))],
            'payments.min_amount' => ['required', 'integer', 'min:1', 'max:10000'],
            'payments.max_amount' => ['required', 'integer', 'min:1', 'max:100000'],
            'payments.receipt_email' => ['nullable', 'email', 'max:255'],
            'payments.stripe_enabled' => ['sometimes', 'boolean'],
            'payments.stripe_product_name' => ['required', 'string', 'max:255'],
            'payments.stripe_product_description' => ['required', 'string', 'max:500'],
            'payments.paypal_enabled' => ['sometimes', 'boolean'],
            'payments.paypal_mode' => ['required', Rule::in(['sandbox', 'live'])],
            'payments.paypal_item_name' => ['required', 'string', 'max:255'],
            'payments.paypal_item_description' => ['required', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function emailRules(): array
    {
        return [
            'email.from_name' => ['required', 'string', 'max:255'],
            'email.from_email' => ['nullable', 'email', 'max:255'],
            'email.admin_notification_email' => ['nullable', 'email', 'max:255'],
            'email.donor_receipts_enabled' => ['sometimes', 'boolean'],
            'email.admin_alerts_enabled' => ['sometimes', 'boolean'],
            'email.footer_en' => ['nullable', 'string', 'max:1000'],
            'email.footer_ar' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function newsletterRules(): array
    {
        return [
            'newsletter.enabled' => ['sometimes', 'boolean'],
            'newsletter.title_en' => ['required', 'string', 'max:255'],
            'newsletter.title_ar' => ['required', 'string', 'max:255'],
            'newsletter.subtitle_en' => ['required', 'string', 'max:500'],
            'newsletter.subtitle_ar' => ['required', 'string', 'max:500'],
            'newsletter.placeholder_en' => ['required', 'string', 'max:120'],
            'newsletter.placeholder_ar' => ['required', 'string', 'max:120'],
            'newsletter.button_en' => ['required', 'string', 'max:60'],
            'newsletter.button_ar' => ['required', 'string', 'max:60'],
            'newsletter.success_en' => ['required', 'string', 'max:255'],
            'newsletter.success_ar' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function volunteersRules(): array
    {
        $rules = [];

        foreach (['confirmation', 'admin_alert', 'welcome', 'rejected'] as $type) {
            $rules["volunteers.{$type}_enabled"] = ['sometimes', 'boolean'];

            foreach (['subject', 'heading'] as $field) {
                $rules["volunteers.{$type}_{$field}_en"] = ['nullable', 'string', 'max:255'];
                $rules["volunteers.{$type}_{$field}_ar"] = ['nullable', 'string', 'max:255'];
            }

            $rules["volunteers.{$type}_body_en"] = ['nullable', 'string', 'max:5000'];
            $rules["volunteers.{$type}_body_ar"] = ['nullable', 'string', 'max:5000'];
        }

        return $rules;
    }

    /**
     * @return array<string, mixed>
     */
    private function legalRules(): array
    {
        return [
            'legal.pages' => ['nullable', 'array'],
            'legal.pages.*' => ['array'],
            'legal.pages.*.title_en' => ['nullable', 'string', 'max:255'],
            'legal.pages.*.title_ar' => ['nullable', 'string', 'max:255'],
            'legal.pages.*.subtitle_en' => ['nullable', 'string', 'max:1000'],
            'legal.pages.*.subtitle_ar' => ['nullable', 'string', 'max:1000'],
            'legal.pages.*.updated_en' => ['nullable', 'string', 'max:120'],
            'legal.pages.*.updated_ar' => ['nullable', 'string', 'max:120'],
            'legal.pages.*.intro_en' => ['nullable', 'string', 'max:5000'],
            'legal.pages.*.intro_ar' => ['nullable', 'string', 'max:5000'],
            'legal.pages.*.sections' => ['nullable', 'array'],
            'legal.pages.*.sections.*.heading_en' => ['nullable', 'string', 'max:255'],
            'legal.pages.*.sections.*.heading_ar' => ['nullable', 'string', 'max:255'],
            'legal.pages.*.sections.*.paragraphs_en' => ['nullable'],
            'legal.pages.*.sections.*.paragraphs_ar' => ['nullable'],
            'legal.pages.*.sections.*.bullets_en' => ['nullable'],
            'legal.pages.*.sections.*.bullets_ar' => ['nullable'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function teamRules(): array
    {
        return [
            'team.page' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function aboutRules(): array
    {
        return [
            'about.page' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function maintenanceRules(): array
    {
        return [
            'maintenance.enabled' => ['sometimes', 'boolean'],
            'maintenance.eyebrow_en' => ['nullable', 'string', 'max:120'],
            'maintenance.eyebrow_ar' => ['nullable', 'string', 'max:120'],
            'maintenance.title_en' => ['nullable', 'string', 'max:255'],
            'maintenance.title_ar' => ['nullable', 'string', 'max:255'],
            'maintenance.message_en' => ['nullable', 'string', 'max:2000'],
            'maintenance.message_ar' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function seoRules(): array
    {
        return [
            'seo.title_en' => ['nullable', 'string', 'max:255'],
            'seo.title_ar' => ['nullable', 'string', 'max:255'],
            'seo.description_en' => ['nullable', 'string', 'max:500'],
            'seo.description_ar' => ['nullable', 'string', 'max:500'],
            'seo.image_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'seo.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'seo.robots_default' => ['nullable', 'string', 'max:120'],
            'seo.canonical_mode' => ['nullable', 'string', Rule::in(['current', 'homepage_prefer'])],
            'seo.twitter_site' => ['nullable', 'string', 'max:80'],
            'seo.organization_type' => ['nullable', 'string', Rule::in(['NGO', 'Organization', 'LocalBusiness'])],
            'seo.organization_name_en' => ['nullable', 'string', 'max:255'],
            'seo.organization_name_ar' => ['nullable', 'string', 'max:255'],
            'seo.schema_organization' => ['sometimes', 'boolean'],
            'seo.schema_website' => ['sometimes', 'boolean'],
            'seo.schema_article' => ['sometimes', 'boolean'],
            'seo.schema_breadcrumb' => ['sometimes', 'boolean'],
            'seo.schema_faq' => ['sometimes', 'boolean'],
            'seo.robots_txt' => ['nullable', 'array'],
            'seo.robots_txt.user_agent' => ['nullable', 'string', 'max:80'],
            'seo.robots_txt.allow' => ['nullable'],
            'seo.robots_txt.disallow' => ['nullable'],
            'seo.robots_txt.host' => ['nullable', 'string', 'max:255'],
            'seo.robots_txt.sitemap_url' => ['nullable', 'string', 'max:500'],
            'seo.robots_txt.extra' => ['nullable', 'string', 'max:5000'],
            'seo.sitemap_enabled' => ['sometimes', 'boolean'],
            'seo.sitemap_include_pages' => ['sometimes', 'boolean'],
            'seo.sitemap_include_posts' => ['sometimes', 'boolean'],
            'seo.sitemap_include_campaigns' => ['sometimes', 'boolean'],
            'seo.sitemap_include_categories' => ['sometimes', 'boolean'],
            'seo.sitemap_changefreq' => ['nullable', 'string', Rule::in(['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'])],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function googleRules(): array
    {
        $consent = Rule::in(['granted', 'denied']);

        return [
            'google.analytics.enabled' => ['sometimes', 'boolean'],
            'google.analytics.measurement_id' => ['nullable', 'string', 'max:32', 'regex:/^$|^G-[A-Z0-9]+$/i'],
            'google.analytics.anonymize_ip' => ['sometimes', 'boolean'],
            'google.analytics.debug' => ['sometimes', 'boolean'],
            'google.analytics.enhanced_measurement' => ['sometimes', 'boolean'],
            'google.analytics.ecommerce' => ['sometimes', 'boolean'],
            'google.gtm.enabled' => ['sometimes', 'boolean'],
            'google.gtm.container_id' => ['nullable', 'string', 'max:32', 'regex:/^$|^GTM-[A-Z0-9]+$/i'],
            'google.gtm.inject_head' => ['sometimes', 'boolean'],
            'google.gtm.inject_body' => ['sometimes', 'boolean'],
            'google.search_console.meta_tag' => ['nullable', 'string', 'max:255'],
            'google.search_console.verification_file' => ['nullable', 'string', 'max:120'],
            'google.search_console.verification_upload' => ['nullable', 'file', 'mimes:html,htm,txt', 'max:64'],
            'google.consent.enabled' => ['sometimes', 'boolean'],
            'google.consent.analytics_storage' => ['nullable', $consent],
            'google.consent.ad_storage' => ['nullable', $consent],
            'google.consent.ad_user_data' => ['nullable', $consent],
            'google.consent.ad_personalization' => ['nullable', $consent],
            'google.consent.wait_for_update' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'google.consent.regions' => ['nullable', 'string', 'max:255'],
            'google.consent.cookie_days' => ['nullable', 'integer', 'min:1', 'max:730'],
            'google.adsense.enabled' => ['sometimes', 'boolean'],
            'google.adsense.publisher_id' => ['nullable', 'string', 'max:40', 'regex:/^$|^ca-pub-\d+$/i'],
            'google.adsense.auto_ads' => ['sometimes', 'boolean'],
            'google.maps.enabled' => ['sometimes', 'boolean'],
            'google.maps.api_key' => ['nullable', 'string', 'max:255'],
            'google.maps.language' => ['nullable', 'string', 'max:10'],
            'google.maps.region' => ['nullable', 'string', 'max:10'],
            'google.recaptcha.enabled' => ['sometimes', 'boolean'],
            'google.recaptcha.site_key' => ['nullable', 'string', 'max:255'],
            'google.recaptcha.secret_key' => ['nullable', 'string', 'max:255'],
            'google.recaptcha.score_threshold' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'google.recaptcha.contact' => ['sometimes', 'boolean'],
            'google.recaptcha.login' => ['sometimes', 'boolean'],
            'google.recaptcha.register' => ['sometimes', 'boolean'],
            'google.merchant.meta_tag' => ['nullable', 'string', 'max:255'],
            'google.fonts.enable_cdn' => ['sometimes', 'boolean'],
            'google.fonts.preconnect' => ['sometimes', 'boolean'],
            'google.fonts.display_swap' => ['sometimes', 'boolean'],
            'google.fonts.prefer_local' => ['sometimes', 'boolean'],
            'google.fonts.family_en' => ['nullable', 'string', 'max:80'],
            'google.fonts.family_ar' => ['nullable', 'string', 'max:80'],
        ];
    }
}
