<?php

namespace App\Http\Requests\Admin\Donors;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SendDonorEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $mediaIds = $this->input('attachment_media_ids');

        if (is_string($mediaIds)) {
            $decoded = json_decode($mediaIds, true);
            $mediaIds = is_array($decoded) ? $decoded : [];
        }

        $youtubeUrls = collect($this->input('youtube_urls', []))
            ->map(fn ($url) => trim((string) $url))
            ->filter()
            ->values()
            ->all();

        $this->merge([
            'attachment_media_ids' => collect($mediaIds ?? [])->map(fn ($id) => (int) $id)->filter()->values()->all(),
            'youtube_urls' => $youtubeUrls,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'cta_text' => ['nullable', 'string', 'max:120'],
            'cta_url' => ['nullable', 'url', 'max:500', 'required_with:cta_text'],
            'attachment_media_ids' => ['nullable', 'array', 'max:12'],
            'attachment_media_ids.*' => ['integer', 'exists:media,id'],
            'youtube_urls' => ['nullable', 'array', 'max:5'],
            'youtube_urls.*' => ['url', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ($this->input('youtube_urls', []) as $index => $url) {
                if (! \App\Support\YouTubeUrl::extractId($url)) {
                    $validator->errors()->add(
                        "youtube_urls.{$index}",
                        __('admin.donors.youtube_invalid'),
                    );
                }
            }

            $mediaIds = $this->input('attachment_media_ids', []);

            if ($mediaIds === []) {
                return;
            }

            $invalid = \App\Models\Media::query()
                ->whereIn('id', $mediaIds)
                ->where('mime_type', 'not like', 'image/%')
                ->exists();

            if ($invalid) {
                $validator->errors()->add('attachment_media_ids', __('admin.donors.attachments_images_invalid'));
            }
        });
    }
}
