@extends('auth.layouts.admin-login')



@section('title', __('admin.login.title'))



@section('content')

    <h1 class="mb-2 text-[28px] font-bold text-[#2f4327]">{{ __('admin.login.heading') }}</h1>

    <p class="mb-8 text-[15px] text-[#8a9280]">{{ __('admin.login.subheading') }}</p>



    <form method="POST" action="{{ route('admin.login.store') }}" class="flex flex-col gap-[18px]" data-admin-login-form novalidate>

        @csrf



        <label class="flex flex-col gap-2 text-[13px] font-semibold text-[#4a5340]">

            {{ __('admin.login.email') }}

            <div class="relative">

                <span class="pointer-events-none absolute start-3.5 top-1/2 flex -translate-y-1/2 text-[#8a9280]">

                    @include('auth.partials.icon', ['name' => 'mail'])

                </span>

                <input

                    id="email"

                    name="email"

                    type="email"

                    value="{{ old('email') }}"

                    required

                    autofocus

                    autocomplete="username"

                    placeholder="{{ __('admin.login.email_placeholder') }}"

                    class="gh-admin-field !ps-[42px]"

                >

            </div>

            @error('email')

                <span class="text-xs font-medium text-[#a24a37]">{{ $message }}</span>

            @enderror

        </label>



        <label class="flex flex-col gap-2 text-[13px] font-semibold text-[#4a5340]">

            <span class="flex items-center justify-between">

                {{ __('admin.login.password') }}

                <span class="text-[12.5px] font-semibold text-[#819562]">{{ __('admin.login.forgot') }}</span>

            </span>

            <div class="relative">

                <span class="pointer-events-none absolute start-3.5 top-1/2 flex -translate-y-1/2 text-[#8a9280]">

                    @include('auth.partials.icon', ['name' => 'lock'])

                </span>

                <input

                    id="password"

                    name="password"

                    type="password"

                    required

                    autocomplete="current-password"

                    placeholder="{{ __('admin.login.password_placeholder') }}"

                    class="gh-admin-field !ps-[42px] !pe-11"

                    data-admin-password-input

                >

                <button

                    type="button"

                    class="absolute end-2.5 top-1/2 flex -translate-y-1/2 cursor-pointer border-none bg-transparent p-1.5 text-[#8a9280]"

                    aria-label="{{ __('admin.login.toggle_password') }}"

                    data-admin-password-toggle

                >

                    @include('auth.partials.icon', ['name' => 'eye'])

                </button>

            </div>

            @error('password')

                <span class="text-xs font-medium text-[#a24a37]">{{ $message }}</span>

            @enderror

        </label>



        <label class="flex cursor-pointer select-none items-center gap-2.5 text-[13.5px] text-[#4a5340]">

            <input

                id="remember"

                name="remember"

                type="checkbox"

                value="1"

                @checked(old('remember', true))

                class="h-[17px] w-[17px] cursor-pointer accent-[#406139]"

            >

            {{ __('admin.login.remember') }}

        </label>

        <button

            type="submit"

            class="flex cursor-pointer items-center justify-center gap-2.5 rounded-xl border-none bg-[#406139] px-4 py-[15px] text-[15px] font-bold text-[#F2F1EA] transition hover:-translate-y-px hover:bg-[#33502e] disabled:cursor-wait disabled:opacity-80"

            data-admin-login-submit

        >

            <span class="hidden h-[17px] w-[17px] animate-spin rounded-full border-[2.5px] border-[rgba(247,246,240,0.4)] border-t-[#F7F6F0]" data-admin-login-spinner></span>

            <span data-admin-login-submit-label data-signing-label="{{ __('admin.login.signing_in') }}">{{ __('admin.login.submit') }}</span>

        </button>

    </form>



    <div class="mt-6 flex items-center justify-center gap-2 border-t border-[rgba(64,97,57,0.12)] pt-5 text-center text-[12.5px] text-[#8a9280]">

        @include('auth.partials.icon', ['name' => 'shield'])

        <span>{{ __('admin.login.secure') }}</span>

    </div>

@endsection

