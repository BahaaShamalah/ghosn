@extends('admin.layouts.app')

@section('title', __('admin.cms.posts_title'))
@section('page-title', __('admin.cms.posts_title'))

@section('content')

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="{{ __('admin.cms.search') }}" class="ghosn-input max-w-xs">
            <select name="status" class="ghosn-input max-w-[160px]">
                <option value="">{{ __('admin.cms.all_statuses') }}</option>
                @foreach ([\App\Models\Post::STATUS_DRAFT, \App\Models\Post::STATUS_PUBLISHED] as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ __('admin.cms.status_'.$status) }}</option>
                @endforeach
            </select>
            <select name="category_id" class="ghosn-input max-w-[180px]">
                <option value="">{{ __('admin.cms.all_categories') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected($filters['category_id'] === (string) $category->id)>{{ $category->name_en }}</option>
                @endforeach
            </select>
            <button type="submit" class="gh-admin-filter-btn">{{ __('admin.cms.filter') }}</button>
        </form>
        <a href="{{ route('admin.posts.create') }}" class="gh-admin-btn-primary no-underline">+ {{ __('admin.cms.new_post') }}</a>
    </div>

    <x-admin.table-card>
        <table class="min-w-full text-[13.5px]">
            <thead>
                <tr>
                    <th class="px-5 py-3.5 text-start">{{ __('admin.cms.featured_image') }}</th>
                    <th class="px-5 py-3.5 text-start">{{ __('admin.cms.title_en') }}</th>
                    <th class="px-5 py-3.5 text-start">{{ __('admin.cms.status') }}</th>
                    <th class="px-5 py-3.5 text-start">{{ __('admin.cms.category') }}</th>
                    <th class="px-5 py-3.5 text-start">{{ __('admin.cms.published_at') }}</th>
                    <th class="px-5 py-3.5 text-end">{{ __('admin.cms.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr class="border-t border-[rgba(64,97,57,0.09)] align-middle">
                        <td class="px-5 py-3.5">
                            @if ($post->featuredImage)
                                <img src="{{ $post->featuredImage->thumbnailUrl() ?? $post->featuredImage->url() }}" alt="" class="h-12 w-16 rounded-[11px] border border-[rgba(64,97,57,0.1)] object-cover">
                            @else
                                <div class="flex h-12 w-16 items-center justify-center rounded-[11px] border border-dashed border-[rgba(64,97,57,0.15)] bg-[rgba(237,238,228,0.4)] text-[10px] font-semibold text-[#8a9280]">—</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-[#2f4327]">{{ $post->title_en }}</p>
                            <p class="mt-0.5 text-xs text-[#8a9280]" dir="ltr">{{ $post->slug }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            @include('admin.partials.status-pill', [
                                'status' => $post->status === \App\Models\Post::STATUS_PUBLISHED ? 'published' : 'draft',
                                'label' => __('admin.cms.status_'.$post->status),
                            ])
                        </td>
                        <td class="px-5 py-3.5">{{ $post->category?->name_en ?? '—' }}</td>
                        <td class="px-5 py-3.5 whitespace-nowrap">{{ $post->published_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex flex-wrap justify-end gap-2">
                                @if ($post->isPublished())
                                    <a href="{{ route('news.show', $post->slug) }}" target="_blank" rel="noopener" class="gh-admin-btn-secondary !px-3 !py-1.5 !text-xs">{{ __('admin.cms.view_public') }}</a>
                                @endif
                                <a href="{{ route('admin.posts.preview', $post) }}" target="_blank" rel="noopener" class="gh-admin-btn-secondary !px-3 !py-1.5 !text-xs">{{ __('admin.cms.preview') }}</a>
                                <a href="{{ route('admin.posts.edit', $post) }}" class="gh-admin-btn-secondary !px-3 !py-1.5 !text-xs">{{ __('admin.cms.edit') }}</a>
                                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm(@json(__('admin.cms.confirm_delete')))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="gh-admin-btn-danger">{{ __('admin.cms.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-[#8a9280]">{{ __('admin.cms.empty_posts') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-admin.table-card>

    <div class="mt-6">{{ $posts->links() }}</div>
@endsection
