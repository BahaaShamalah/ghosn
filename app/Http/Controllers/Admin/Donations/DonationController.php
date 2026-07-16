<?php

namespace App\Http\Controllers\Admin\Donations;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use App\Services\Donations\DonationAdminStats;
use App\Services\Donations\DonationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonationController extends Controller
{
    public function __construct(
        private readonly DonationService $donations,
        private readonly DonationAdminStats $stats,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'status' => (string) $request->query('status', ''),
            'gateway' => (string) $request->query('gateway', ''),
            'payment_method' => (string) $request->query('payment_method', ''),
            'campaign_id' => (string) $request->query('campaign_id', ''),
            'date_from' => (string) $request->query('date_from', ''),
            'date_to' => (string) $request->query('date_to', ''),
            'search' => trim((string) $request->query('search', '')),
        ];

        $donations = $this->filteredQuery($filters)
            ->with('campaign')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.donations.index', [
            'donations' => $donations,
            'filters' => $filters,
            'stats' => $this->stats->summary($filters['campaign_id'] !== '' ? (int) $filters['campaign_id'] : null),
            'campaigns' => Campaign::query()->orderBy('title_en')->get(['id', 'title_en']),
            'statuses' => config('donations.statuses', []),
            'gateways' => [
                Donation::GATEWAY_STRIPE,
                Donation::GATEWAY_PAYPAL,
                Donation::GATEWAY_BANK,
            ],
            'paymentMethods' => array_values(config('donations.payment_methods', [])),
        ]);
    }

    public function confirm(Donation $donation): RedirectResponse
    {
        if (! $donation->canManuallyConfirm()) {
            return back()->with('error', $donation->isGatewayPayment()
                ? __('admin.donations.confirm_gateway_blocked')
                : __('admin.donations.confirm_failed'));
        }

        $this->donations->markBankTransferConfirmed($donation);

        return back()->with('status', __('admin.donations.confirmed'));
    }

    /**
     * @param  array<string, string>  $filters
     * @return Builder<Donation>
     */
    private function filteredQuery(array $filters): Builder
    {
        return Donation::query()
            ->when($filters['status'] !== '', fn (Builder $query) => $query->where('status', $filters['status']))
            ->when($filters['campaign_id'] !== '', fn (Builder $query) => $query->where('campaign_id', $filters['campaign_id']))
            ->when($filters['gateway'] !== '', fn (Builder $query) => $query->where('gateway', $filters['gateway']))
            ->when($filters['payment_method'] !== '', fn (Builder $query) => $query->where('payment_method', $filters['payment_method']))
            ->when($filters['date_from'] !== '', fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when($filters['date_to'] !== '', fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']))
            ->when($filters['search'] !== '', function (Builder $query) use ($filters): void {
                $term = '%'.$filters['search'].'%';
                $query->where(function (Builder $inner) use ($term): void {
                    $inner->where('reference', 'like', $term)
                        ->orWhere('donor_name', 'like', $term)
                        ->orWhere('donor_email', 'like', $term);
                });
            });
    }
}
