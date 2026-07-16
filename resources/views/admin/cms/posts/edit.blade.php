@extends('admin.layouts.app')

@section('title', __('admin.cms.edit_post'))
@section('page-title', __('admin.cms.edit_post'))

@section('content')
    <form method="POST" action="{{ route('admin.posts.update', $post) }}" class="space-y-6">
        @csrf
        @method('PUT')
        <div class="gh-admin-card rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 md:p-7">
            @include('admin.cms.partials.post-fields', ['post' => $post])
        </div>
        @php
            $extra = '<a href="'.e(route('admin.posts.preview', $post)).'" target="_blank" class="gh-admin-btn-secondary">'.e(__('admin.cms.preview')).'</a>';
            if ($post->isPublished()) {
                $extra .= '<a href="'.e(route('news.show', $post->slug)).'" target="_blank" class="gh-admin-btn-secondary">'.e(__('admin.cms.view_public')).'</a>';
            }
        @endphp
        @include('admin.partials.form-actions', [
            'cancelUrl' => route('admin.posts.index'),
            'extra' => $extra,
        ])
    </form>
    <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="mt-4" onsubmit="return confirm(@json(__('admin.cms.confirm_delete')))">
        @csrf
        @method('DELETE')
        <button type="submit" class="gh-admin-btn-danger">{{ __('admin.cms.delete') }}</button>
    </form>
@endsection

@push('scripts')
    @include('admin.partials.cms-scripts')
@endpush
