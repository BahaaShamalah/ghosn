@extends('admin.layouts.app')

@section('title', __('admin.cms.new_category'))
@section('page-title', __('admin.cms.new_category'))

@section('content')
    <form method="POST" action="{{ route('admin.categories.store') }}" class="max-w-2xl space-y-6">
        @csrf
        <div class="gh-admin-card rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 md:p-7">
            @include('admin.cms.partials.category-fields', ['category' => new \App\Models\Category()])
        </div>
        @include('admin.partials.form-actions', [
            'cancelUrl' => route('admin.categories.index'),
        ])
    </form>
@endsection
