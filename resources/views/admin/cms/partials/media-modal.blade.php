<div id="cms-media-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-ghosn/40 p-4" role="dialog" aria-modal="true" aria-labelledby="cms-media-modal-title">
    <div class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-3xl border border-ghosn/10 bg-offwhite shadow-2xl">
        <div class="flex items-center justify-between border-b border-ghosn/10 px-5 py-4">
            <h2 id="cms-media-modal-title" class="text-lg font-bold text-ghosn">{{ __('admin.cms.media_library') }}</h2>
            <button type="button" data-media-modal-close class="rounded-full p-2 text-ghosn/60 hover:bg-ghosn/5 hover:text-ghosn" aria-label="{{ __('admin.cms.close') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="border-b border-ghosn/10 px-5 py-3">
            <input type="search" data-media-modal-search placeholder="{{ __('admin.cms.search_media') }}" class="ghosn-input">
        </div>

        <div data-media-modal-grid class="grid flex-1 grid-cols-2 gap-3 overflow-y-auto p-5 sm:grid-cols-3 md:grid-cols-4"></div>

        <div data-media-modal-empty class="hidden px-5 py-10 text-center text-sm text-ghosn-ink/60">
            {{ __('admin.cms.no_media_found') }}
        </div>

        <div data-media-modal-loading class="hidden px-5 py-10 text-center text-sm text-ghosn-ink/60">
            {{ __('admin.cms.loading') }}
        </div>
    </div>
</div>
