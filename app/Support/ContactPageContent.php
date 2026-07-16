<?php

namespace App\Support;

use App\Services\Settings\SettingsService;

class ContactPageContent
{
    /**
     * @return array<string, mixed>
     */
    public static function forReact(): array
    {
        $defaults = self::defaults();
        $stored = self::stored();
        $page = array_replace_recursive($defaults, $stored);

        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);

        $pair = static fn (string $enKey, string $arKey, array $source): array => [
            'en' => (string) ($source[$enKey] ?? ''),
            'ar' => (string) ($source[$arKey] ?? ''),
        ];

        $form = $page['form'] ?? [];
        $links = $page['links'] ?? [];
        $cta = $page['cta'] ?? [];
        $sections = $page['sections'] ?? [];

        return [
            'sections' => [
                'hero' => (bool) ($sections['hero'] ?? true),
                'details' => (bool) ($sections['details'] ?? true),
                'form' => (bool) ($sections['form'] ?? true),
                'cta' => (bool) ($sections['cta'] ?? true),
            ],
            'hero' => [
                'eyebrow' => [
                    'en' => (string) $settings->get('contact.page_hero_eyebrow_en', 'We are here to help'),
                    'ar' => (string) $settings->get('contact.page_hero_eyebrow_ar', 'نحن هنا لمساعدتك'),
                ],
                'title' => [
                    'en' => (string) $settings->get('contact.page_hero_title_en', 'Get in touch with GHOSN'),
                    'ar' => (string) $settings->get('contact.page_hero_title_ar', 'تواصل مع غُصن'),
                ],
                'subtitle' => [
                    'en' => (string) $settings->get('contact.page_hero_subtitle_en', 'Questions, partnerships, or just a hello — reach out and our team will get back to you shortly.'),
                    'ar' => (string) $settings->get('contact.page_hero_subtitle_ar', 'أسئلة أو شراكات أو حتى تحية — راسلنا وسيعود إليك فريقنا في أقرب وقت.'),
                ],
            ],
            'info' => [
                'eyebrow' => [
                    'en' => (string) $settings->get('contact.page_info_eyebrow_en', 'Contact details'),
                    'ar' => (string) $settings->get('contact.page_info_eyebrow_ar', 'بيانات التواصل'),
                ],
                'title' => [
                    'en' => (string) $settings->get('contact.page_info_title_en', 'Reach us directly'),
                    'ar' => (string) $settings->get('contact.page_info_title_ar', 'تواصل معنا مباشرة'),
                ],
                'body' => [
                    'en' => (string) $settings->get('contact.page_info_body_en', 'Prefer a quick message? Use any channel below, or fill in the form and we will reply as soon as we can.'),
                    'ar' => (string) $settings->get('contact.page_info_body_ar', 'تفضّل رسالة سريعة؟ استخدم أي وسيلة أدناه، أو املأ النموذج وسنردّ عليك بأسرع ما يمكن.'),
                ],
            ],
            'office' => [
                'en' => (string) $settings->get('contact.office_en', ''),
                'ar' => (string) $settings->get('contact.office_ar', ''),
            ],
            'form' => [
                'title' => $pair('title_en', 'title_ar', $form),
                'subtitle' => $pair('subtitle_en', 'subtitle_ar', $form),
                'name' => $pair('name_en', 'name_ar', $form),
                'namePh' => $pair('name_ph_en', 'name_ph_ar', $form),
                'email' => $pair('email_en', 'email_ar', $form),
                'emailPh' => $pair('email_ph_en', 'email_ph_ar', $form),
                'subject' => $pair('subject_en', 'subject_ar', $form),
                'subjectPh' => $pair('subject_ph_en', 'subject_ph_ar', $form),
                'subjects' => [
                    'en' => self::stringList($form['subjects_en'] ?? []),
                    'ar' => self::stringList($form['subjects_ar'] ?? []),
                ],
                'message' => $pair('message_en', 'message_ar', $form),
                'messagePh' => $pair('message_ph_en', 'message_ph_ar', $form),
                'submit' => $pair('submit_en', 'submit_ar', $form),
                'sending' => $pair('sending_en', 'sending_ar', $form),
                'success' => $pair('success_en', 'success_ar', $form),
                'error' => $pair('error_en', 'error_ar', $form),
            ],
            'links' => [
                'title' => $pair('title_en', 'title_ar', $links),
                'follow' => $pair('follow_en', 'follow_ar', $links),
            ],
            'cta' => [
                'title' => $pair('title_en', 'title_ar', $cta),
                'subtitle' => $pair('subtitle_en', 'subtitle_ar', $cta),
                'primary' => $pair('primary_en', 'primary_ar', $cta),
                'secondary' => $pair('secondary_en', 'secondary_ar', $cta),
                'primaryUrl' => (string) ($cta['primary_url'] ?? '/donate'),
                'secondaryUrl' => (string) ($cta['secondary_url'] ?? '/volunteer'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return config('contact-page', []);
    }

    /**
     * @return array<string, mixed>
     */
    private static function stored(): array
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $raw = $settings->get('contact.page');

        return is_array($raw) ? $raw : [];
    }

    /**
     * @param  mixed  $value
     * @return list<string>
     */
    private static function stringList(mixed $value): array
    {
        if (is_string($value)) {
            $value = preg_split("/\r\n|\n|\r/", $value) ?: [];
        }

        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($item): string => trim((string) $item),
            $value,
        )));
    }
}
