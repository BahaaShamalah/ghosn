@extends('admin.layouts.app')

@section('title', __('admin.cms.edit_page'))
@section('page-title', __('admin.cms.edit_page'))

@section('content')
    <form method="POST" action="{{ route('admin.content-pages.update', $page) }}" class="space-y-6">
        @csrf
        @method('PUT')
        <div class="gh-admin-card rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 md:p-7">
            @include('admin.cms.partials.page-fields', ['page' => $page])
        </div>
        @php
            $extra = '<a href="'.e(route('admin.content-pages.preview', $page)).'" target="_blank" class="gh-admin-btn-secondary">'.e(__('admin.cms.preview')).'</a>';
            if ($page->isPublished()) {
                $extra .= '<a href="'.e(route('pages.show', $page->slug)).'" target="_blank" class="gh-admin-btn-secondary">'.e(__('admin.cms.view_public')).'</a>';
            }
        @endphp
        @include('admin.partials.form-actions', [
            'cancelUrl' => route('admin.content-pages.index'),
            'extra' => $extra,
        ])
    </form>
    @if ($page->canDelete())
        <form method="POST" action="{{ route('admin.content-pages.destroy', $page) }}" class="mt-4" onsubmit="return confirm(@json(__('admin.cms.confirm_delete')))">
            @csrf
            @method('DELETE')
            <button type="submit" class="gh-admin-btn-danger">{{ __('admin.cms.delete') }}</button>
        </form>
    @endif
@endsection

@push('scripts')
    @include('admin.partials.cms-scripts')
@endpush
