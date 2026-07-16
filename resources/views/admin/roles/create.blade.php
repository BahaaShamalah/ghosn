@extends('admin.layouts.app')

@section('title', __('admin.roles.new'))
@section('page-title', __('admin.roles.new'))

@section('content')
    @include('admin.partials.back-link', [
        'url' => route('admin.roles.index'),
        'label' => __('admin.roles.back_to_list'),
    ])

    <form method="POST" action="{{ route('admin.roles.store') }}" class="max-w-4xl space-y-6">
        @csrf
        @include('admin.roles.partials.role-fields')
        @include('admin.partials.form-actions', [
            'cancelUrl' => route('admin.roles.index'),
            'submitLabel' => __('admin.roles.save'),
        ])
    </form>
@endsection
