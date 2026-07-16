<?php

namespace App\Http\Requests\Admin\Pages;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageSectionVolunteerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],

            'content.hero_eyebrow_en' => ['nullable', 'string', 'max:255'],
            'content.hero_eyebrow_ar' => ['nullable', 'string', 'max:255'],
            'content.hero_title_en' => ['nullable', 'string', 'max:500'],
            'content.hero_title_ar' => ['nullable', 'string', 'max:500'],
            'content.hero_subtitle_en' => ['nullable', 'string', 'max:1000'],
            'content.hero_subtitle_ar' => ['nullable', 'string', 'max:1000'],
            'content.hero_cta_en' => ['nullable', 'string', 'max:255'],
            'content.hero_cta_ar' => ['nullable', 'string', 'max:255'],
            'content.hero_image_media_id' => [
                'nullable',
                'integer',
                Rule::exists('media', 'id')->where(fn ($query) => $query->where('mime_type', 'like', 'image/%')),
            ],

            'content.why_eyebrow_en' => ['nullable', 'string', 'max:255'],
            'content.why_eyebrow_ar' => ['nullable', 'string', 'max:255'],
            'content.why_title_en' => ['nullable', 'string', 'max:500'],
            'content.why_title_ar' => ['nullable', 'string', 'max:500'],
            'content.why_intro_en' => ['nullable', 'string', 'max:1000'],
            'content.why_intro_ar' => ['nullable', 'string', 'max:1000'],

            'content.areas_eyebrow_en' => ['nullable', 'string', 'max:255'],
            'content.areas_eyebrow_ar' => ['nullable', 'string', 'max:255'],
            'content.areas_title_en' => ['nullable', 'string', 'max:500'],
            'content.areas_title_ar' => ['nullable', 'string', 'max:500'],

            'content.how_eyebrow_en' => ['nullable', 'string', 'max:255'],
            'content.how_eyebrow_ar' => ['nullable', 'string', 'max:255'],
            'content.how_title_en' => ['nullable', 'string', 'max:500'],
            'content.how_title_ar' => ['nullable', 'string', 'max:500'],

            'content.testimonial_quote_en' => ['nullable', 'string', 'max:2000'],
            'content.testimonial_quote_ar' => ['nullable', 'string', 'max:2000'],
            'content.testimonial_name_en' => ['nullable', 'string', 'max:255'],
            'content.testimonial_name_ar' => ['nullable', 'string', 'max:255'],
            'content.testimonial_role_en' => ['nullable', 'string', 'max:255'],
            'content.testimonial_role_ar' => ['nullable', 'string', 'max:255'],
            'content.testimonial_initial_en' => ['nullable', 'string', 'max:10'],
            'content.testimonial_initial_ar' => ['nullable', 'string', 'max:10'],

            'content.apply_eyebrow_en' => ['nullable', 'string', 'max:255'],
            'content.apply_eyebrow_ar' => ['nullable', 'string', 'max:255'],
            'content.apply_title_en' => ['nullable', 'string', 'max:500'],
            'content.apply_title_ar' => ['nullable', 'string', 'max:500'],
            'content.apply_intro_en' => ['nullable', 'string', 'max:1000'],
            'content.apply_intro_ar' => ['nullable', 'string', 'max:1000'],

            'content.thanks_title_en' => ['nullable', 'string', 'max:500'],
            'content.thanks_title_ar' => ['nullable', 'string', 'max:500'],
            'content.thanks_body_en' => ['nullable', 'string', 'max:1000'],
            'content.thanks_body_ar' => ['nullable', 'string', 'max:1000'],
            'content.thanks_home_en' => ['nullable', 'string', 'max:255'],
            'content.thanks_home_ar' => ['nullable', 'string', 'max:255'],
            'content.thanks_explore_en' => ['nullable', 'string', 'max:255'],
            'content.thanks_explore_ar' => ['nullable', 'string', 'max:255'],
        ];

        foreach (['benefits', 'area_items', 'steps', 'form_areas'] as $repeater) {
            $rules["content.{$repeater}"] = ['nullable', 'array'];
            $rules["content.{$repeater}.*.title_en"] = ['nullable', 'string', 'max:500'];
            $rules["content.{$repeater}.*.title_ar"] = ['nullable', 'string', 'max:500'];
            $rules["content.{$repeater}.*.body_en"] = ['nullable', 'string', 'max:1000'];
            $rules["content.{$repeater}.*.body_ar"] = ['nullable', 'string', 'max:1000'];
            $rules["content.{$repeater}.*.label_en"] = ['nullable', 'string', 'max:255'];
            $rules["content.{$repeater}.*.label_ar"] = ['nullable', 'string', 'max:255'];
            $rules["content.{$repeater}.*.value"] = ['nullable', 'string', 'max:80'];
        }

        foreach ([
            'name_label', 'age_label', 'phone_label', 'email_label', 'area_label',
            'availability_label', 'message_label', 'agree_label', 'submit', 'sending', 'error',
            'avail_weekdays', 'avail_weekends', 'avail_remote',
        ] as $field) {
            $rules["content.{$field}_en"] = ['nullable', 'string', 'max:255'];
            $rules["content.{$field}_ar"] = ['nullable', 'string', 'max:255'];
        }

        foreach (['name', 'age', 'phone', 'email', 'message'] as $field) {
            $rules["content.{$field}_placeholder_en"] = ['nullable', 'string', 'max:255'];
            $rules["content.{$field}_placeholder_ar"] = ['nullable', 'string', 'max:255'];
        }

        $rules['content.area_placeholder_en'] = ['nullable', 'string', 'max:255'];
        $rules['content.area_placeholder_ar'] = ['nullable', 'string', 'max:255'];

        $rules['hero_image_upload'] = ['nullable', 'image', 'max:5120'];

        return $rules;
    }

    protected function getRedirectUrl(): string
    {
        return route('admin.pages.sections.volunteer.edit', [
            $this->route('page'),
            $this->route('section'),
        ]);
    }
}
