@php
    $routePatterns = [
        'admin.dashboard' => ['admin.dashboard'],
        'admin.settings.index' => ['admin.settings.*'],
        'admin.pages.index' => ['admin.pages.*'],
        'admin.posts.index' => ['admin.posts.*'],
        'admin.content-pages.index' => ['admin.content-pages.*'],
        'admin.categories.index' => ['admin.categories.*'],
        'admin.media.index' => ['admin.media.*'],
        'admin.campaigns.index' => ['admin.campaigns.*'],
        'admin.donations.index' => ['admin.donations.*'],
        'admin.volunteers.index' => ['admin.volunteers.*'],
        'admin.newsletter.index' => ['admin.newsletter.*'],
        'admin.messages.index' => ['admin.messages.*'],
        'admin.donors.index' => ['admin.donors.*'],
        'admin.users.index' => ['admin.users.*'],
        'admin.roles.index' => ['admin.roles.*'],
    ];
@endphp

<button
    type="button"
    aria-label="Close menu"
    data-admin-sidebar-overlay
    class="gh-admin-overlay fixed inset-0 z-40 bg-black/40 lg:hidden"
></button>

<aside
    data-admin-sidebar
    class="gh-admin-sidebar z-50 flex h-screen w-[260px] shrink-0 flex-col bg-[#243619] px-[18px] py-6 text-[#cdd8bf] max-lg:fixed max-lg:inset-y-0 max-lg:start-0"
>
    <div class="mb-[18px] flex items-center gap-[11px] border-b border-[rgba(150,167,145,0.18)] px-2 pb-6">
        <img src="{{ \App\Support\SiteAsset::logoUrl() }}" alt="GHOSN" class="h-[38px] w-auto brightness-0 invert">
        <div class="flex flex-col leading-none">
            <span class="text-[17px] font-bold tracking-[3px] text-[#F2F1EA]">GHOSN</span>
            <span class="mt-[3px] text-[8px] font-semibold tracking-[3px] text-[#96A791]">{{ app()->getLocale() === 'ar' ? 'لوحة الإدارة' : 'ADMIN PANEL' }}</span>
        </div>
    </div>

    <nav class="gh-admin-sidebar-nav flex flex-1 flex-col gap-1 overflow-y-auto" aria-label="{{ __('admin.nav.label') }}">
        @foreach ($adminNav as $item)
            @php
                $patterns = $routePatterns[$item['route']] ?? [$item['route']];
                $active = collect($patterns)->contains(fn (string $pattern) => request()->routeIs($pattern));
            @endphp
            <a
                href="{{ route($item['route']) }}"
                @class([
                    'flex items-center gap-[13px] rounded-xl px-[14px] py-3 text-[14.5px] font-semibold no-underline transition',
                    'active' => $active,
                    'text-[#cdd8bf]' => ! $active,
                ])
            >
                <span class="flex shrink-0">
                    @include('admin.partials.nav-icon', ['name' => $item['icon']])
                </span>
                <span class="flex-1">{{ $item['label'] }}</span>
                @if (($adminNavBadges[$item['key']] ?? 0) > 0)
                    <span class="inline-flex min-w-[22px] items-center justify-center rounded-full bg-[#a24a37] px-2 py-0.5 text-[10px] font-bold text-white">
                        {{ $adminNavBadges[$item['key']] > 99 ? '99+' : $adminNavBadges[$item['key']] }}
                    </span>
                @elseif (! empty($item['placeholder']))
                    <span class="rounded-full bg-[#819562] px-2 py-0.5 text-[10px] font-bold text-white">Soon</span>
                @endif
            </a>
        @endforeach
    </nav>

    <div class="mt-3 flex items-center gap-[11px] border-t border-[rgba(150,167,145,0.18)] pt-4">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#819562] to-[#406139] text-[15px] font-bold text-[#F7F6F0]">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </span>
        <div class="min-w-0 flex-1">
            <div class="truncate text-[13.5px] font-bold text-[#F2F1EA]">{{ auth()->user()->name }}</div>
            <div class="text-[11.5px] text-[#8fa080]">{{ auth()->user()->roleLabel() }}</div>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" aria-label="{{ __('admin.dashboard.logout') }}" class="flex cursor-pointer border-none bg-transparent text-[#8fa080] transition hover:text-[#cdd8bf]">
                @include('admin.partials.nav-icon', ['name' => 'logout'])
            </button>
        </form>
    </div>
</aside>
