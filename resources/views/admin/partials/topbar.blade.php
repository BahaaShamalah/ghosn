<header class="gh-admin-topbar z-40 border-b border-[rgba(64,97,57,0.12)] bg-[rgba(237,238,228,0.9)] backdrop-blur-md max-lg:sticky max-lg:top-0">
    <div class="flex flex-wrap items-center gap-5 px-6 py-4 md:px-8">
        <button
            type="button"
            data-admin-sidebar-toggle
            class="flex h-10 w-10 items-center justify-center rounded-xl border border-[rgba(64,97,57,0.18)] bg-[#fffdf8] text-[#406139] lg:hidden"
            aria-label="Open menu"
        >
            @include('admin.partials.nav-icon', ['name' => 'menu'])
        </button>

        <div class="min-w-0 flex-1">
            <h1 class="m-0 text-[22px] font-bold text-[#2f4327]">@yield('page-title', __('admin.dashboard.title'))</h1>
            <div class="mt-0.5 text-[13px] text-[#8a9280]">
                {{ __('admin.dashboard.welcome') }}, {{ auth()->user()->name }}
            </div>
            @if (! empty($adminBreadcrumbs) && count($adminBreadcrumbs) > 1)
                <nav class="mt-2 hidden lg:block" aria-label="{{ __('admin.topbar.breadcrumbs') }}">
                    <ol class="flex flex-wrap items-center gap-1.5 text-[12px] text-[#8a9280]">
                        @foreach ($adminBreadcrumbs as $index => $crumb)
                            @if ($index > 0)
                                <li class="text-[#c5cbbf]" aria-hidden="true">/</li>
                            @endif
                            <li @class(['truncate', 'font-semibold text-[#406139]' => ! $crumb['url']])>
                                @if ($crumb['url'])
                                    <a href="{{ $crumb['url'] }}" class="text-[#8a9280] no-underline transition hover:text-[#406139]">{{ $crumb['label'] }}</a>
                                @else
                                    <span>{{ $crumb['label'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-[14px]">
            <label class="relative hidden items-center sm:flex">
                <span class="sr-only">{{ __('admin.topbar.search') }}</span>
                <span class="pointer-events-none absolute inset-inline-start-3 flex text-[#8a9280]">
                    @include('admin.partials.nav-icon', ['name' => 'search'])
                </span>
                <input
                    type="search"
                    disabled
                    placeholder="{{ __('admin.topbar.search_placeholder') }}"
                    class="w-[210px] rounded-[11px] border border-[rgba(64,97,57,0.18)] bg-[#fffdf8] py-2.5 ps-9 pe-3.5 text-[13.5px] text-[#3a4234] outline-none"
                >
            </label>

            <div class="flex overflow-hidden rounded-full border border-[rgba(64,97,57,0.24)]">
                @foreach (config('locale.supported', ['en', 'ar']) as $locale)
                    <a
                        href="{{ route('admin.locale.switch', $locale) }}"
                        @class([
                            'px-[13px] py-[7px] text-xs font-semibold no-underline transition',
                            'bg-[#406139] text-[#F2F1EA]' => app()->getLocale() === $locale,
                            'bg-transparent text-[#406139]' => app()->getLocale() !== $locale,
                        ])
                    >
                        {{ strtoupper($locale) }}
                    </a>
                @endforeach
            </div>

            @if ($adminInboxUrl)
                <a
                    href="{{ $adminInboxUrl }}"
                    class="relative hidden h-[42px] w-[42px] items-center justify-center rounded-xl border border-[rgba(64,97,57,0.18)] bg-[#fffdf8] text-[#406139] no-underline sm:inline-flex"
                    aria-label="{{ __('admin.topbar.notifications') }}"
                    title="{{ __('admin.topbar.notifications') }}"
                >
                    @include('admin.partials.nav-icon', ['name' => 'bell'])
                    @if (($adminInboxCount ?? 0) > 0)
                        <span class="absolute -top-1 -end-1 inline-flex min-h-[18px] min-w-[18px] items-center justify-center rounded-full bg-[#a24a37] px-1 text-[10px] font-bold text-white">
                            {{ $adminInboxCount > 9 ? '9+' : $adminInboxCount }}
                        </span>
                    @endif
                </a>
            @else
                <button
                    type="button"
                    class="relative hidden h-[42px] w-[42px] items-center justify-center rounded-xl border border-[rgba(64,97,57,0.18)] bg-[#fffdf8] text-[#406139] sm:inline-flex"
                    aria-label="{{ __('admin.topbar.notifications') }}"
                    data-tooltip="{{ __('admin.topbar.notifications_soon') }}"
                >
                    @include('admin.partials.nav-icon', ['name' => 'bell'])
                </button>
            @endif

            <div class="relative">
                <button
                    type="button"
                    data-admin-user-toggle
                    class="flex items-center gap-2 rounded-full border border-[rgba(64,97,57,0.18)] bg-[#fffdf8] py-1.5 ps-1.5 pe-3 transition hover:bg-[#f7f6f0]"
                >
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-[#819562] to-[#406139] text-sm font-bold text-[#F7F6F0]">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <span class="hidden text-start sm:block">
                        <span class="block text-sm font-semibold text-[#2f4327]">{{ auth()->user()->name }}</span>
                        <span class="block max-w-[140px] truncate text-[11px] text-[#8a9280]">{{ auth()->user()->email }}</span>
                    </span>
                    <svg class="hidden h-4 w-4 text-[#8a9280] sm:block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08z" clip-rule="evenodd" /></svg>
                </button>

                <div data-admin-user-menu class="absolute end-0 z-50 mt-2 hidden w-56 overflow-hidden rounded-2xl border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] py-1 shadow-xl shadow-[rgba(47,67,39,0.12)]">
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-sm text-[#3a4234] no-underline hover:bg-[rgba(64,97,57,0.06)]">{{ __('admin.topbar.my_profile') }}</a>
                    <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2.5 text-sm text-[#3a4234] no-underline hover:bg-[rgba(64,97,57,0.06)]">{{ __('admin.topbar.account_settings') }}</a>
                    <a href="{{ route('admin.password.edit') }}" class="block px-4 py-2.5 text-sm text-[#3a4234] no-underline hover:bg-[rgba(64,97,57,0.06)]">{{ __('admin.topbar.change_password') }}</a>
                    <div class="my-1 border-t border-[rgba(64,97,57,0.08)]"></div>
                    <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="block px-4 py-2.5 text-sm text-[#3a4234] no-underline hover:bg-[rgba(64,97,57,0.06)]">{{ __('admin.view_site') }}</a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-2.5 text-start text-sm font-semibold text-[#a24a37] hover:bg-[rgba(162,74,55,0.08)]">{{ __('admin.dashboard.logout') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

@include('admin.partials.delete-modal')
