@extends('admin.layouts.app')

@section('title', __('admin.users.new'))
@section('page-title', __('admin.users.new'))

@section('content')
    @include('admin.partials.back-link', [
        'url' => route('admin.users.index'),
        'label' => __('admin.users.back_to_list'),
    ])

    <form method="POST" action="{{ route('admin.users.store') }}" class="max-w-3xl space-y-6">
        @csrf
        @include('admin.users.partials.user-fields')
        @include('admin.partials.form-actions', [
            'cancelUrl' => route('admin.users.index'),
            'submitLabel' => __('admin.users.save'),
        ])
    </form>
@endsection
