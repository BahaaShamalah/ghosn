@extends('admin.layouts.app')

@section('title', __('admin.media.title'))
@section('page-title', __('admin.media.title'))
@section('eyebrow', __('admin.panel'))

@section('content')

    <div class="gh-admin-card mb-8 rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 md:p-8">
        <h2 class="text-lg font-bold text-[#2f4327]">{{ __('admin.media.upload') }}</h2>
        <p class="mt-2 text-sm text-[#8a9280]">{{ __('admin.media.upload_help', ['types' => $allowedTypes, 'max' => $maxUploadMb]) }}</p>

        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="mt-5 flex flex-wrap items-end gap-4">
            @csrf
            <div class="min-w-[240px] flex-1">
                <input type="file" name="file" required class="block w-full text-sm text-[#2f4327] file:me-4 file:rounded-[11px] file:border-0 file:bg-[#406139] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#f2f1ea] hover:file:bg-[#33502e]">
                @error('file')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="gh-admin-btn-primary">{{ __('admin.media.upload_button') }}</button>
        </form>
    </div>

    <form method="GET" action="{{ route('admin.media.index') }}" class="mb-6 flex flex-wrap gap-3">
        <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="{{ __('admin.media.search') }}" class="ghosn-input max-w-xs">
        <select name="type" class="ghosn-input max-w-[160px]">
            <option value="">{{ __('admin.media.all_types') }}</option>
            <option value="image" @selected($filters['type'] === 'image')>{{ __('admin.media.type_image') }}</option>
            <option value="video" @selected($filters['type'] === 'video')>{{ __('admin.media.type_video') }}</option>
            <option value="document" @selected($filters['type'] === 'document')>{{ __('admin.media.type_document') }}</option>
        </select>
        <button type="submit" class="gh-admin-filter-btn">{{ __('admin.media.filter') }}</button>
    </form>

    @if ($mediaItems->isEmpty())
        <div class="gh-admin-empty">{{ __('admin.media.empty') }}</div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($mediaItems as $item)
                <article class="gh-admin-card overflow-hidden rounded-[18px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8]">
                    <div class="flex aspect-[4/3] items-center justify-center bg-[rgba(237,238,228,0.5)] p-4">
                        @if ($item->isImage() && ! $item->isSvg())
                            <img src="{{ $item->thumbnailUrl() ?? $item->url() }}" alt="{{ e($item->original_filename) }}" class="max-h-full max-w-full object-contain">
                        @elseif ($item->isSvg())
                            <img src="{{ $item->url() }}" alt="{{ e($item->original_filename) }}" class="max-h-full max-w-full object-contain">
                        @elseif (str_starts_with($item->mime_type, 'video/'))
                            <span class="text-4xl text-[#406139]/40">▶</span>
                        @else
                            <span class="text-sm font-semibold uppercase text-[#8a9280]">{{ pathinfo($item->original_filename, PATHINFO_EXTENSION) }}</span>
                        @endif
                    </div>
                    <div class="space-y-2 p-4 text-xs text-[#8a9280]">
                        <p class="truncate font-semibold text-[#2f4327]" title="{{ $item->original_filename }}">{{ $item->original_filename }}</p>
                        <p>{{ $item->mime_type }} · {{ $item->humanSize() }}</p>
                        @if ($item->width && $item->height)
                            <p>{{ $item->width }}×{{ $item->height }} px</p>
                        @endif
                        <p>{{ $item->created_at?->format('Y-m-d H:i') }}</p>
                        <div class="flex flex-wrap gap-2 pt-2">
                            <button type="button" data-copy-url="{{ $item->url() }}" class="copy-url gh-admin-btn-secondary !px-3 !py-1 !text-[11px]">{{ __('admin.media.copy_url') }}</button>
                            <form method="POST" action="{{ route('admin.media.destroy', $item) }}" onsubmit="return confirm(@json(__('admin.media.delete_confirm')))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="gh-admin-btn-danger !px-3 !py-1 !text-[11px]">{{ __('admin.media.delete') }}</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">{{ $mediaItems->links() }}</div>
    @endif
@endsection

@push('scripts')
<script>
document.querySelectorAll('.copy-url').forEach((btn) => {
    btn.addEventListener('click', async () => {
        const url = btn.getAttribute('data-copy-url');
        try {
            await navigator.clipboard.writeText(url);
            btn.textContent = @json(__('admin.media.copied'));
            setTimeout(() => { btn.textContent = @json(__('admin.media.copy_url')); }, 1500);
        } catch {
            prompt(@json(__('admin.media.copy_url')), url);
        }
    });
});
</script>
@endpush
