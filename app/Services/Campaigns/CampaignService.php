<?php

namespace App\Services\Campaigns;

use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Support\Facades\DB;

class CampaignService
{
    public function applyPaidDonation(Donation $donation): void
    {
        if (! $donation->campaign_id || ! $donation->isPaid()) {
            return;
        }

        DB::transaction(function () use ($donation): void {
            /** @var Campaign|null $campaign */
            $campaign = Campaign::query()->lockForUpdate()->find($donation->campaign_id);

            if (! $campaign) {
                return;
            }

            $campaign->increment('raised_amount', $donation->amount);
            $campaign->increment('donors_count');

            $campaign->refresh();

            if (
                $campaign->status === Campaign::STATUS_ACTIVE
                && (float) $campaign->goal_amount > 0
                && (float) $campaign->raised_amount >= (float) $campaign->goal_amount
            ) {
                $campaign->update(['status' => Campaign::STATUS_COMPLETED]);
            }
        });
    }

    public function findPublicBySlug(string $slug): ?Campaign
    {
        return Campaign::query()
            ->public()
            ->where('slug', $slug)
            ->first();
    }
}
