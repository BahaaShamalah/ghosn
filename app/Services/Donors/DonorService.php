<?php

namespace App\Services\Donors;

use App\Models\Donation;
use App\Models\Donor;
use Illuminate\Support\Facades\DB;

class DonorService
{
    public function syncFromDonation(Donation $donation): Donor
    {
        $email = strtolower(trim((string) $donation->donor_email));

        if ($email === '') {
            throw new \InvalidArgumentException('Donation donor email is required.');
        }

        $donor = Donor::query()->firstOrNew(['email' => $email]);

        $donor->fill([
            'name' => $donation->donor_name,
            'phone' => $donation->donor_phone ?? $donor->phone,
            'is_anonymous' => (bool) $donation->is_anonymous,
            'locale' => $donation->locale ?: ($donor->locale ?: 'en'),
        ]);

        if (! $donor->exists) {
            $donor->status = Donor::STATUS_ACTIVE;
        }

        $donor->save();

        if ($donation->donor_id !== $donor->id) {
            $donation->forceFill(['donor_id' => $donor->id])->saveQuietly();
        }

        if ($donation->isPaid()) {
            $this->refreshStats($donor);
        }

        return $donor->fresh();
    }

    public function refreshStats(Donor $donor): Donor
    {
        $stats = Donation::query()
            ->where('donor_id', $donor->id)
            ->where('status', Donation::STATUS_PAID)
            ->selectRaw('COUNT(*) as donations_count, COALESCE(SUM(amount), 0) as total_donated, MAX(paid_at) as last_donation_at')
            ->first();

        $donor->update([
            'donations_count' => (int) ($stats->donations_count ?? 0),
            'total_donated' => (float) ($stats->total_donated ?? 0),
            'last_donation_at' => $stats->last_donation_at,
        ]);

        return $donor->fresh();
    }

    public function toggleBlock(Donor $donor): Donor
    {
        $donor->update([
            'status' => $donor->isBlocked() ? Donor::STATUS_ACTIVE : Donor::STATUS_BLOCKED,
        ]);

        return $donor->fresh();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function supportedCampaigns(Donor $donor): array
    {
        return DB::table('donations')
            ->join('campaigns', 'donations.campaign_id', '=', 'campaigns.id')
            ->where('donations.donor_id', $donor->id)
            ->where('donations.status', Donation::STATUS_PAID)
            ->whereNotNull('donations.campaign_id')
            ->groupBy('campaigns.id', 'campaigns.title_en', 'campaigns.title_ar')
            ->selectRaw('campaigns.id, campaigns.title_en, campaigns.title_ar, COUNT(*) as donation_count, SUM(donations.amount) as total_amount')
            ->orderByDesc('total_amount')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }
}
