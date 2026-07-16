@extends('admin.layouts.app')

@section('title', __('admin.users.edit'))
@section('page-title', __('admin.users.edit'))

@section('content')
    @include('admin.partials.back-link', [
        'url' => route('admin.users.index'),
        'label' => __('admin.users.back_to_list'),
    ])

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="max-w-3xl space-y-6">
        @csrf @method('PUT')
        @include('admin.users.partials.user-fields', ['user' => $user])
        @include('admin.partials.form-actions', [
            'cancelUrl' => route('admin.users.index'),
            'submitLabel' => __('admin.users.save'),
        ])
    </form>
@endsection
