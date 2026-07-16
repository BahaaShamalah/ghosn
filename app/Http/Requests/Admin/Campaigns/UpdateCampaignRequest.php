<?php

namespace App\Http\Requests\Admin\Campaigns;

use App\Models\Campaign;
use Illuminate\Validation\Rule;

class UpdateCampaignRequest extends StoreCampaignRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Campaign $campaign */
        $campaign = $this->route('campaign');

        return [
            ...$this->sharedRules(),
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('campaigns', 'slug')->ignore($campaign->id)],
        ];
    }
}
