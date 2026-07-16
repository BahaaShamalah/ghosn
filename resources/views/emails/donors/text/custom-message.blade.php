{{ $emailSubject }}

{{ __('emails.custom.greeting', ['name' => $donorName], $locale) }}

{{ $messageBody }}
@if (! empty($imageUrls))
@foreach ($imageUrls as $imageUrl)
{{ $imageUrl }}
@endforeach
@endif
@if (! empty($youtubeVideos))
@foreach ($youtubeVideos as $video)
{{ __('emails.custom.watch_video', locale: $locale) }}: {{ $video['watch_url'] }}
@endforeach
@endif
@if ($ctaText && $ctaUrl)
{{ $ctaText }}: {{ $ctaUrl }}
@endif
