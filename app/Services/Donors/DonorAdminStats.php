<?php

namespace App\Services\Donors;

use App\Models\Donor;
use Illuminate\Support\Facades\DB;

class DonorAdminStats
{
    /**
     * @param  array<string, string>  $filters
     * @return array<string, int|float>
     */
    public function summary(array $filters = []): array
    {
        $base = $this->filteredQuery($filters);

        $totals = (clone $base)
            ->selectRaw('COUNT(*) as total_donors, COALESCE(SUM(total_donated), 0) as total_donated')
            ->first();

        $repeatDonors = (clone $base)
            ->where('donations_count', '>=', 2)
            ->count();

        $last30Days = (clone $base)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return [
            'total_donors' => (int) ($totals->total_donors ?? 0),
            'total_donated' => (float) ($totals->total_donated ?? 0),
            'repeat_donors' => $repeatDonors,
            'last_30_days_donors' => $last30Days,
        ];
    }

    /**
     * @param  array<string, string>  $filters
     */
    private function filteredQuery(array $filters): \Illuminate\Database\Eloquent\Builder
    {
        return Donor::query()
            ->when(($filters['status'] ?? '') !== '', fn ($q) => $q->where('status', $filters['status']))
            ->when(($filters['min_donated'] ?? '') !== '', fn ($q) => $q->where('total_donated', '>=', (float) $filters['min_donated']))
            ->when(($filters['max_donated'] ?? '') !== '', fn ($q) => $q->where('total_donated', '<=', (float) $filters['max_donated']))
            ->when(($filters['date_from'] ?? '') !== '', fn ($q) => $q->whereDate('last_donation_at', '>=', $filters['date_from']))
            ->when(($filters['date_to'] ?? '') !== '', fn ($q) => $q->whereDate('last_donation_at', '<=', $filters['date_to']))
            ->when(trim($filters['search'] ?? '') !== '', function ($q) use ($filters): void {
                $term = '%'.trim($filters['search']).'%';
                $q->where(function ($inner) use ($term): void {
                    $inner->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
            });
    }
}
