<?php

namespace App\Support;

class ContactReactPayload
{
    /**
     * @return array<string, mixed>
     */
    public static function build(): array
    {
        return array_merge(BuilderReactPayload::sharedChrome(), [
            'pageType' => 'contact',
            'pageTitle' => [
                'en' => 'Contact',
                'ar' => 'تواصل معنا',
            ],
            'contactPage' => ContactPageContent::forReact(),
        ]);
    }
}
