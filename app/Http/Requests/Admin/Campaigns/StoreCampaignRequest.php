<?php

namespace App\Http\Requests\Admin\Campaigns;

use App\Models\Campaign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $videoSource = (string) $this->input('video_source', $this->filled('video_url') && ! $this->filled('video_media_id') ? 'url' : 'upload');
        $videoMediaId = $this->input('video_media_id');
        $videoUrl = trim((string) $this->input('video_url', ''));

        // Only one video source is accepted — drop the other based on the selected mode.
        if ($videoSource === 'url') {
            $videoMediaId = null;
        } else {
            $videoUrl = '';
        }

        $this->merge([
            'is_featured_homepage' => $this->boolean('is_featured_homepage'),
            'goal_amount' => $this->filled('goal_amount') ? (float) $this->input('goal_amount') : 0,
            'raised_amount' => $this->filled('raised_amount') ? (float) $this->input('raised_amount') : 0,
            'sort_order' => (int) $this->input('sort_order', 0),
            'gallery_media_ids' => $this->normalizeGalleryIds(),
            'video_media_id' => $videoMediaId === '' ? null : $videoMediaId,
            'video_url' => $videoUrl === '' ? null : $videoUrl,
        ]);

        foreach (['story_en', 'story_ar'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => \App\Support\ContentHtml::sanitizeStorage($this->input($field)),
                ]);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->sharedRules();
    }

    /**
     * @return array<string, mixed>
     */
    protected function sharedRules(): array
    {
        $currencies = array_keys(config('donations.currencies', ['USD' => []]));

        return [
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('campaigns', 'slug')],
            'excerpt_en' => ['nullable', 'string', 'max:1000'],
            'excerpt_ar' => ['nullable', 'string', 'max:1000'],
            'story_en' => ['nullable', 'string', 'max:50000'],
            'story_ar' => ['nullable', 'string', 'max:50000'],
            'featured_image_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'video_media_id' => [
                'nullable',
                'integer',
                Rule::exists('media', 'id')->where(
                    fn ($query) => $query->where('mime_type', 'like', 'video/%')
                ),
            ],
            'video_url' => ['nullable', 'string', 'max:500', 'url'],
            'gallery_media_ids' => ['nullable', 'array', 'max:20'],
            'gallery_media_ids.*' => ['integer', 'exists:media,id'],
            'goal_amount' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'raised_amount' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'currency' => ['required', Rule::in($currencies ?: ['USD'])],
            'status' => ['required', Rule::in(config('campaigns.statuses', []))],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'is_featured_homepage' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'seo_title_en' => ['nullable', 'string', 'max:255'],
            'seo_title_ar' => ['nullable', 'string', 'max:255'],
            'seo_description_en' => ['nullable', 'string', 'max:500'],
            'seo_description_ar' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return list<int>
     */
    private function normalizeGalleryIds(): array
    {
        $raw = $this->input('gallery_media_ids');

        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);

            if (is_array($decoded)) {
                $raw = $decoded;
            }
        }

        if (! is_array($raw)) {
            return [];
        }

        return array_values(array_unique(array_map('intval', $raw)));
    }
}
