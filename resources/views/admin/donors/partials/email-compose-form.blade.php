@php
    $attachmentIds = old('attachment_media_ids');

    if (is_string($attachmentIds)) {
        $attachmentIds = json_decode($attachmentIds, true) ?: [];
    }

    $attachmentIds = collect($attachmentIds ?? [])->map(fn ($id) => (int) $id)->filter()->values();
    $attachmentMedia = $attachmentIds->isNotEmpty()
        ? \App\Models\Media::query()->whereIn('id', $attachmentIds)->get()->keyBy('id')
        : collect();

    $youtubeUrls = old('youtube_urls', ['']);
    if (! is_array($youtubeUrls) || $youtubeUrls === []) {
        $youtubeUrls = [''];
    }
@endphp

<div class="gh-admin-card rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-gradient-to-br from-[rgba(237,238,228,0.5)] to-[#fffdf8] p-6">
    <div class="mb-5 flex items-start justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-ghosn">{{ __('admin.donors.send_email') }}</h2>
            <p class="mt-1 text-sm text-ghosn-ink/60">{{ __('admin.donors.send_email_help') }}</p>
        </div>
        <span class="shrink-0 rounded-full bg-ghosn/10 px-3 py-1 text-xs font-semibold text-ghosn" dir="ltr">{{ $donor->email }}</span>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc ps-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.donors.send-email', $donor) }}" class="space-y-5">
        @csrf

        <div class="grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.donors.subject') }}</label>
                <input type="text" name="subject" value="{{ old('subject') }}" class="ghosn-input" required maxlength="255" placeholder="{{ __('admin.donors.subject_placeholder') }}">
            </div>

            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.donors.message') }}</label>
                <textarea name="message" rows="6" class="ghosn-input" required maxlength="5000" placeholder="{{ __('admin.donors.message_placeholder') }}">{{ old('message') }}</textarea>
            </div>
        </div>

        <div class="rounded-2xl border border-ghosn/10 bg-offwhite/80 p-4">
            <label class="mb-1 block text-sm font-semibold text-ghosn">{{ __('admin.donors.attachments_images') }}</label>
            <p class="mb-3 text-xs text-ghosn-ink/55">{{ __('admin.donors.attachments_images_help') }}</p>

            <div data-media-gallery>
                <input type="hidden" name="attachment_media_ids" value="{{ $attachmentIds->toJson() }}" data-gallery-input>
                <div class="mb-3 flex flex-wrap gap-2" data-gallery-preview>
                    @foreach ($attachmentIds as $mediaId)
                        @php $media = $attachmentMedia->get($mediaId); @endphp
                        @if ($media)
                            <div class="relative overflow-hidden rounded-xl border border-ghosn/10" data-gallery-item="{{ $media->id }}">
                                <img src="{{ $media->thumbnailUrl() ?? $media->url() }}" alt="" class="h-24 w-32 object-cover">
                                <button type="button" data-gallery-remove="{{ $media->id }}" class="absolute right-1 top-1 rounded-full bg-ghosn/85 px-1.5 text-xs text-offwhite">×</button>
                            </div>
                        @endif
                    @endforeach
                </div>
                <button type="button" data-gallery-add class="gh-admin-btn-secondary">
                    {{ __('admin.donors.add_images') }}
                </button>
            </div>
        </div>

        <div class="rounded-2xl border border-ghosn/10 bg-offwhite/80 p-4" data-youtube-repeater>
            <label class="mb-1 block text-sm font-semibold text-ghosn">{{ __('admin.donors.attachments_youtube') }}</label>
            <p class="mb-3 text-xs text-ghosn-ink/55">{{ __('admin.donors.attachments_youtube_help') }}</p>

            <div class="space-y-2" data-youtube-list>
                @foreach ($youtubeUrls as $index => $youtubeUrl)
                    <div class="flex gap-2" data-youtube-row>
                        <input
                            type="url"
                            name="youtube_urls[]"
                            value="{{ $youtubeUrl }}"
                            class="ghosn-input flex-1"
                            maxlength="500"
                            placeholder="https://www.youtube.com/watch?v=..."
                            dir="ltr"
                        >
                        <button type="button" data-youtube-remove class="shrink-0 rounded-full border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50" @if($index === 0 && count($youtubeUrls) === 1) hidden @endif>×</button>
                    </div>
                @endforeach
            </div>

            <button type="button" data-youtube-add class="mt-3 gh-admin-btn-secondary">
                {{ __('admin.donors.add_youtube_link') }}
            </button>
        </div>

        <div class="grid gap-4 rounded-2xl border border-ghosn/10 bg-cream/25 p-4 md:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.donors.cta_text') }}</label>
                <input type="text" name="cta_text" value="{{ old('cta_text') }}" class="ghosn-input" maxlength="120" placeholder="{{ __('admin.donors.cta_text_placeholder') }}">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.donors.cta_url') }}</label>
                <input type="url" name="cta_url" value="{{ old('cta_url') }}" class="ghosn-input" maxlength="500" placeholder="https://" dir="ltr">
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-ghosn/10 pt-4">
            <p class="text-xs text-ghosn-ink/50">{{ __('admin.donors.send_email_note') }}</p>
            <button
                type="submit"
                class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700 disabled:cursor-not-allowed disabled:opacity-50"
                @disabled($donor->isBlocked())
            >
                {{ __('admin.donors.send') }}
            </button>
        </div>
    </form>
</div>
