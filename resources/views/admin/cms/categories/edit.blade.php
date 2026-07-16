@extends('admin.layouts.app')

@section('title', __('admin.cms.edit_category'))
@section('page-title', __('admin.cms.edit_category'))

@section('content')
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="max-w-2xl space-y-6">
        @csrf
        @method('PUT')
        <div class="gh-admin-card rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 md:p-7">
            @include('admin.cms.partials.category-fields', ['category' => $category])
        </div>
        @include('admin.partials.form-actions', [
            'cancelUrl' => route('admin.categories.index'),
        ])
    </form>
    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="mt-4" onsubmit="return confirm(@json(__('admin.cms.confirm_delete')))">
        @csrf
        @method('DELETE')
        <button type="submit" class="gh-admin-btn-danger">{{ __('admin.cms.delete') }}</button>
    </form>
@endsection
