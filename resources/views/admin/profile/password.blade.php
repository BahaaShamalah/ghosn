@extends('admin.layouts.app')

@section('title', __('admin.profile.password.title'))
@section('page-title', __('admin.profile.password.title'))

@section('content')
    @include('admin.partials.back-link', [
        'url' => route('admin.dashboard'),
        'label' => __('admin.profile.password.back'),
    ])

    <form method="POST" action="{{ route('admin.password.update') }}" class="max-w-xl space-y-6">
        @csrf
        @method('PUT')

        <div class="gh-admin-card space-y-5 rounded-[18px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6">
            <p class="text-sm text-[#8a9280]">{{ __('admin.profile.password.intro') }}</p>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.profile.password.current') }}</label>
                <input type="password" name="current_password" required autocomplete="current-password" class="ghosn-input">
                @include('admin.partials.field-error', ['field' => 'current_password'])
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.profile.password.new') }}</label>
                    <input type="password" name="password" required autocomplete="new-password" class="ghosn-input">
                    @include('admin.partials.field-error', ['field' => 'password'])
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.profile.password.confirm') }}</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password" class="ghosn-input">
                </div>
            </div>
        </div>

        @include('admin.partials.form-actions', [
            'cancelUrl' => route('admin.dashboard'),
            'submitLabel' => __('admin.profile.password.save'),
        ])
    </form>
@endsection
