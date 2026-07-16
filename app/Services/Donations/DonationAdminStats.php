<?php

namespace App\Services\Donations;

use App\Models\Donation;

class DonationAdminStats
{
    /**
     * @return array{
     *     total_count: int,
     *     paid_amount: float,
     *     pending_amount: float,
     *     bank_pending_count: int,
     *     stripe_paid_count: int,
     *     paypal_paid_count: int
     * }
     */
    public function summary(?int $campaignId = null): array
    {
        $base = Donation::query();

        if ($campaignId) {
            $base->where('campaign_id', $campaignId);
        }

        $paidAmount = (float) (clone $base)
            ->where('status', Donation::STATUS_PAID)
            ->sum('amount');

        $pendingAmount = (float) (clone $base)
            ->where('status', Donation::STATUS_PENDING)
            ->sum('amount');

        return [
            'total_count' => (clone $base)->count(),
            'paid_amount' => $paidAmount,
            'pending_amount' => $pendingAmount,
            'bank_pending_count' => (clone $base)
                ->where('status', Donation::STATUS_PENDING)
                ->where('payment_method', Donation::METHOD_BANK)
                ->count(),
            'stripe_paid_count' => (clone $base)
                ->where('status', Donation::STATUS_PAID)
                ->where('gateway', Donation::GATEWAY_STRIPE)
                ->count(),
            'paypal_paid_count' => (clone $base)
                ->where('status', Donation::STATUS_PAID)
                ->where('gateway', Donation::GATEWAY_PAYPAL)
                ->count(),
        ];
    }
}
