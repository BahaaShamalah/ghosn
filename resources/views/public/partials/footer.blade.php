@php
    $nav = \App\Support\PublicNavigation::links();
    $footer = \App\Support\SiteChrome::footerCopy();
    $footerLinks = \App\Support\SiteChrome::footerLinks();
    $socialLinks = \App\Support\SiteFooter::socialLinks();
    $contact = \App\Support\SiteFooter::contact();
    $locale = app()->getLocale();
@endphp

<footer class="gh-site-footer scroll-mt-20">
    <div class="gh-site-footer__grid">
        <div class="gh-site-footer__brand">
            <div class="gh-site-footer__logo-row">
                <img src="{{ \App\Support\SiteAsset::logoUrl() }}" alt="{{ \App\Support\SiteSettings::name() }}" class="gh-site-footer__logo">
                <div class="gh-site-footer__logo-text">
                    <span class="gh-site-footer__logo-title">GHOSN</span>
                    <span class="gh-site-footer__logo-subtitle">RELIEF TEAM</span>
                </div>
            </div>
            <p class="gh-site-footer__desc">
                <span data-en="">{{ $footer['desc']['en'] }}</span>
                <span data-ar="">{{ $footer['desc']['ar'] }}</span>
            </p>
            <p class="gh-site-footer__tagline">
                <span data-en="">{{ $footer['tagline']['en'] }}</span>
                <span data-ar="">{{ $footer['tagline']['ar'] }}</span>
            </p>
            @if ($socialLinks->isNotEmpty())
                <div class="gh-site-footer__follow-label">
                    <span data-en="">{{ $footer['followTitle']['en'] }}</span>
                    <span data-ar="">{{ $footer['followTitle']['ar'] }}</span>
                </div>
                <div class="gh-site-footer__social">
                    @foreach ($socialLinks as $link)
                        <a
                            href="{{ $link->url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="gh-site-footer__social-link"
                            aria-label="{{ $link->localizedLabel() }}"
                            title="{{ $link->localizedLabel() }}"
                        >
                            <i class="{{ $link->fontAwesomeClass() }}" aria-hidden="true"></i>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="gh-site-footer__column">
            <div class="gh-site-footer__column-title">
                <span data-en="">{{ $footer['quickTitle']['en'] }}</span>
                <span data-ar="">{{ $footer['quickTitle']['ar'] }}</span>
            </div>
            <div class="gh-site-footer__links">
                @foreach ($nav as $link)
                    <a href="{{ $link['href'] }}">
                        <span data-en="">{{ $link['label_en'] }}</span>
                        <span data-ar="">{{ $link['label_ar'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="gh-site-footer__column">
            <div class="gh-site-footer__column-title">
                <span data-en="">{{ $footer['linksTitle']['en'] }}</span>
                <span data-ar="">{{ $footer['linksTitle']['ar'] }}</span>
            </div>
            <div class="gh-site-footer__links">
                @foreach ($footerLinks as $link)
                    <a href="{{ $link['href'] }}">
                        <span data-en="">{{ $link['label_en'] }}</span>
                        <span data-ar="">{{ $link['label_ar'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="gh-site-footer__column">
            <div class="gh-site-footer__column-title">
                <span data-en="">{{ $footer['contactTitle']['en'] }}</span>
                <span data-ar="">{{ $footer['contactTitle']['ar'] }}</span>
            </div>
            <div class="gh-site-footer__contact">
                @if (filled($contact['phone']))
                    <div>
                        <div class="gh-site-footer__contact-label">
                            <span data-en="">{{ __('public.contact.phone_label') }}</span>
                            <span data-ar="">{{ __('public.contact.phone_label_ar') }}</span>
                        </div>
                        <div dir="ltr">{{ $contact['phone'] }}</div>
                    </div>
                @endif
                <div>
                    <div class="gh-site-footer__contact-label">
                        <span data-en="">{{ __('public.contact.email_label') }}</span>
                        <span data-ar="">{{ __('public.contact.email_label_ar') }}</span>
                    </div>
                    <a href="mailto:{{ $contact['email'] }}" dir="ltr">{{ $contact['email'] }}</a>
                </div>
                @if (filled($footer['address'][$locale]) || filled($footer['address']['en']))
                    <div>
                        <div class="gh-site-footer__contact-label">
                            <span data-en="">{{ __('public.contact.address_label') }}</span>
                            <span data-ar="">{{ __('public.contact.address_label_ar') }}</span>
                        </div>
                        <div>
                            <span data-en="">{{ $footer['address']['en'] }}</span>
                            <span data-ar="">{{ $footer['address']['ar'] }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="gh-site-footer__bottom">
        &copy; {{ date('Y') }}
        <span data-en="">{{ \App\Support\SiteSettings::name('en') }}. {{ $footer['rights']['en'] }}</span>
        <span data-ar="">{{ \App\Support\SiteSettings::name('ar') }}. {{ $footer['rights']['ar'] }}</span>
    </div>
</footer>
