@php
    $anchor = $section['key'] === 'hero' ? 'home' : $section['key'];
@endphp

<section id="{{ $anchor }}" class="scroll-mt-24 border-b border-ghosn/10 bg-offwhite py-16 md:py-20">
    <div class="mx-auto max-w-4xl px-5 md:px-10">
        <div class="mb-6 inline-flex items-center gap-2.5">
            <span class="h-[1.5px] w-8 bg-growth"></span>
            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-growth">
                <span data-en="">{{ $section['title_en'] }}</span><span data-ar="">{{ $section['title_ar'] }}</span>
            </span>
        </div>

        @foreach ($section['blocks'] as $block)
            @if ($block['type'] === 'heading')
                <h2 class="font-bold tracking-tightish text-ghosn text-[clamp(1.75rem,3vw,2.5rem)] leading-tight">
                    <span data-en="">{{ $block['content']['en'] ?? '' }}</span><span data-ar="">{{ $block['content']['ar'] ?? '' }}</span>
                </h2>
            @elseif ($block['type'] === 'text')
                <p class="mt-4 text-base leading-relaxed text-ghosn-ink/75 md:text-lg">
                    <span data-en="">{{ $block['content']['en'] ?? '' }}</span><span data-ar="">{{ $block['content']['ar'] ?? '' }}</span>
                </p>
            @elseif (in_array($block['type'], ['image', 'video'], true) && ! empty($block['content']['media_id']))
                @php $media = \App\Models\Media::query()->find($block['content']['media_id']); @endphp
                @if ($media)
                    @if ($block['type'] === 'video' && str_starts_with($media->mime_type, 'video/'))
                        <video src="{{ $media->url() }}" class="mt-4 w-full rounded-2xl" controls preload="metadata"></video>
                    @else
                        <img src="{{ $media->url() }}" alt="{{ $block['content']['en'] ?? $media->original_filename }}" class="mt-4 max-w-full rounded-2xl" loading="lazy">
                    @endif
                @endif
            @endif
        @endforeach
    </div>
</section>
