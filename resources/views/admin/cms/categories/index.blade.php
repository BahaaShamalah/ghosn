@extends('admin.layouts.app')

@section('title', __('admin.cms.categories_title'))
@section('page-title', __('admin.cms.categories_title'))

@section('content')

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="search" name="search" value="{{ $search }}" class="ghosn-input max-w-xs" placeholder="{{ __('admin.cms.search') }}">
            <select name="type" class="ghosn-input max-w-[180px]">
                <option value="">{{ __('admin.cms.all_category_types') }}</option>
                <option value="{{ \App\Models\Category::TYPE_POST }}" @selected($type === \App\Models\Category::TYPE_POST)>{{ __('admin.cms.category_type_post') }}</option>
                <option value="{{ \App\Models\Category::TYPE_CAMPAIGN }}" @selected($type === \App\Models\Category::TYPE_CAMPAIGN)>{{ __('admin.cms.category_type_campaign') }}</option>
            </select>
            <button type="submit" class="gh-admin-filter-btn">{{ __('admin.cms.filter') }}</button>
        </form>
        <a href="{{ route('admin.categories.create') }}" class="gh-admin-btn-primary no-underline">+ {{ __('admin.cms.new_category') }}</a>
    </div>

    <x-admin.table-card>
        <table class="min-w-full text-[13.5px]">
            <thead>
                <tr>
                    <th class="px-5 py-3.5 text-start">{{ __('admin.cms.name_en') }}</th>
                    <th class="px-5 py-3.5 text-start">Slug</th>
                    <th class="px-5 py-3.5 text-start">{{ __('admin.cms.category_type') }}</th>
                    <th class="px-5 py-3.5 text-start">{{ __('admin.cms.usage') }}</th>
                    <th class="px-5 py-3.5 text-end"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr class="border-t border-[rgba(64,97,57,0.09)]">
                        <td class="px-5 py-3.5 font-semibold text-[#2f4327]">{{ $category->name_en }}</td>
                        <td class="px-5 py-3.5" dir="ltr">{{ $category->slug }}</td>
                        <td class="px-5 py-3.5">{{ __('admin.cms.category_type_'.$category->type) }}</td>
                        <td class="px-5 py-3.5">
                            @if ($category->type === \App\Models\Category::TYPE_CAMPAIGN)
                                {{ $category->campaigns_count }} {{ __('admin.campaigns.title') }}
                            @else
                                {{ $category->posts_count }} {{ __('admin.cms.posts_title') }}
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-end">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="gh-admin-btn-secondary !px-3 !py-1.5 !text-xs no-underline">{{ __('admin.cms.edit') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-[#8a9280]">{{ __('admin.cms.empty_categories') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-admin.table-card>

    <div class="mt-6">{{ $categories->links() }}</div>
@endsection
