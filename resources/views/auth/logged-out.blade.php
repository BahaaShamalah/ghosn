@extends('auth.layouts.admin-login')



@section('title', __('admin.logout_page.title'))



@section('content')

    <div class="text-center">

        <div class="mx-auto mb-6 flex h-[88px] w-[88px] items-center justify-center rounded-full bg-[rgba(129,149,98,0.16)] gh-admin-login-pop">

            @include('auth.partials.icon', ['name' => 'wave'])

        </div>



        <h1 class="mb-2.5 text-[26px] font-bold text-[#2f4327]">{{ __('admin.logout_page.title') }}</h1>

        <p class="mb-8 text-[15px] text-pretty text-[#8a9280]">{{ __('admin.logout_page.subtitle') }}</p>



        <a

            href="{{ route('admin.login') }}"

            class="block w-full rounded-xl border-none bg-[#406139] px-4 py-[15px] text-[15px] font-bold text-[#F2F1EA] no-underline transition hover:-translate-y-px hover:bg-[#33502e]"

        >

            {{ __('admin.logout_page.again') }}

        </a>



        <a href="{{ route('home') }}" class="mt-3.5 block text-sm font-semibold text-[#406139] no-underline">

            {{ __('admin.logout_page.home') }}

        </a>

    </div>

@endsection

