<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Support\LocaleHelper::direction() }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \App\Support\SiteSettings::title($__env->yieldContent('title', __('admin.login.title'))) }}</title>

    @vite(['resources/css/app.css', 'resources/js/admin-shell.js'])

    @include('partials.theme-fonts')

</head>

<body class="gh-admin-login antialiased">

    <div class="flex min-h-screen flex-col bg-[#F2F1EA] text-[#3a4234] lg:flex-row">

        <div class="gh-admin-login-brand relative flex flex-[1_1_46%] flex-col justify-between overflow-hidden p-10 text-[#F7F6F0] lg:min-h-screen">

            <div class="pointer-events-none absolute -end-10 -top-16 h-[300px] w-[300px] rounded-full border border-dashed border-[rgba(220,228,204,0.2)] gh-admin-login-spin"></div>

            <div class="pointer-events-none absolute bottom-0 start-[10%] h-[240px] w-[240px] rounded-full bg-[radial-gradient(circle,rgba(129,149,98,0.34),transparent_70%)] gh-admin-login-float"></div>



            <a href="{{ route('home') }}" class="relative flex items-center gap-3 no-underline">

                <img

                    src="{{ \App\Support\SiteAsset::logoUrl() ?: asset('assets/landing/images/logo.webp') }}"

                    alt="GHOSN"

                    class="h-11 w-auto brightness-0 invert"

                >

                <div class="leading-none">

                    <span class="text-xl font-bold tracking-[4px] text-[#F2F1EA]">GHOSN</span>

                    <span class="mt-1 block text-[9px] font-semibold tracking-[4px] text-[#96A791]">ADMIN PANEL</span>

                </div>

            </a>



            <div class="relative my-10 max-w-md">

                <h2 class="text-[clamp(26px,3vw,36px)] font-bold leading-tight text-balance">{{ __('admin.login.brand_title') }}</h2>

                <p class="mt-3.5 text-base text-[#DCE4CC] text-pretty">{{ __('admin.login.brand_subtitle') }}</p>

                <div class="mt-8 flex flex-wrap gap-6">

                    <div>

                        <div class="text-2xl font-bold text-[#F7F6F0]">{{ __('admin.login.brand_stat_campaigns_value') }}</div>

                        <div class="text-[12.5px] text-[#96A791]">{{ __('admin.login.brand_stat_campaigns') }}</div>

                    </div>

                    <div>

                        <div class="text-2xl font-bold text-[#F7F6F0]">{{ __('admin.login.brand_stat_volunteers_value') }}</div>

                        <div class="text-[12.5px] text-[#96A791]">{{ __('admin.login.brand_stat_volunteers') }}</div>

                    </div>

                    <div>

                        <div class="text-2xl font-bold text-[#F7F6F0]">{{ __('admin.login.brand_stat_raised_value') }}</div>

                        <div class="text-[12.5px] text-[#96A791]">{{ __('admin.login.brand_stat_raised') }}</div>

                    </div>

                </div>

            </div>



            <p class="relative text-[12.5px] text-[#8fa080]">{{ __('admin.login.brand_footer', ['year' => now()->year]) }}</p>

        </div>



        <div class="relative flex flex-[1_1_54%] flex-col items-center justify-center p-8 md:p-11">

            <div class="absolute end-6 top-6 flex overflow-hidden rounded-full border border-[rgba(64,97,57,0.24)] lg:end-8 lg:top-8">

                @foreach (config('locale.supported', ['en', 'ar']) as $locale)

                    <a

                        href="{{ route('admin.locale.switch', $locale) }}"

                        @class([

                            'px-3.5 py-1.5 text-xs font-semibold no-underline transition',

                            'bg-[#406139] text-[#F2F1EA]' => app()->getLocale() === $locale,

                            'bg-transparent text-[#406139]' => app()->getLocale() !== $locale,

                        ])

                    >

                        {{ strtoupper($locale) }}

                    </a>

                @endforeach

            </div>



            <div class="w-full max-w-[400px] gh-admin-login-fade">

                @yield('content')

            </div>

        </div>

    </div>

</body>

</html>


