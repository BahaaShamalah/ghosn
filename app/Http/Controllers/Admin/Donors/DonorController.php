<?php

namespace App\Http\Controllers\Admin\Donors;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Donors\SendDonorEmailRequest;
use App\Models\Donor;
use App\Models\Media;
use App\Services\Donors\DonorAdminStats;
use App\Services\Donors\DonorEmailService;
use App\Services\Donors\DonorService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DonorController extends Controller
{
    public function __construct(
        private readonly DonorService $donors,
        private readonly DonorEmailService $emails,
        private readonly DonorAdminStats $stats,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'status' => (string) $request->query('status', ''),
            'search' => trim((string) $request->query('search', '')),
            'min_donated' => (string) $request->query('min_donated', ''),
            'max_donated' => (string) $request->query('max_donated', ''),
            'date_from' => (string) $request->query('date_from', ''),
            'date_to' => (string) $request->query('date_to', ''),
        ];

        $donorList = $this->filteredQuery($filters)
            ->latest('last_donation_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.donors.index', [
            'donors' => $donorList,
            'filters' => $filters,
            'stats' => $this->stats->summary($filters),
        ]);
    }

    public function show(Donor $donor): View
    {
        $donor->load([
            'donations' => fn ($q) => $q->with('campaign')->latest(),
            'emailLogs' => fn ($q) => $q->latest()->limit(50),
        ]);

        return view('admin.donors.show', [
            'donor' => $donor,
            'campaigns' => $this->donors->supportedCampaigns($donor),
            'mediaLibrary' => Media::query()->latest()->limit(100)->get(),
        ]);
    }

    public function toggleBlock(Donor $donor): RedirectResponse
    {
        $this->donors->toggleBlock($donor);

        return back()->with('status', $donor->fresh()->isBlocked()
            ? __('admin.donors.blocked_success')
            : __('admin.donors.unblocked_success'));
    }

    public function sendEmail(SendDonorEmailRequest $request, Donor $donor): RedirectResponse
    {
        if ($donor->isBlocked()) {
            return back()->with('error', __('admin.donors.send_blocked'));
        }

        $this->emails->sendCustomMessage($donor, [
            'subject' => $request->validated('subject'),
            'message' => $request->validated('message'),
            'cta_text' => $request->validated('cta_text'),
            'cta_url' => $request->validated('cta_url'),
            'attachment_media_ids' => $request->validated('attachment_media_ids') ?? [],
            'youtube_urls' => $request->validated('youtube_urls') ?? [],
        ]);

        return back()->with('status', __('admin.donors.email_sent'));
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = [
            'status' => (string) $request->query('status', ''),
            'search' => trim((string) $request->query('search', '')),
            'min_donated' => (string) $request->query('min_donated', ''),
            'max_donated' => (string) $request->query('max_donated', ''),
            'date_from' => (string) $request->query('date_from', ''),
            'date_to' => (string) $request->query('date_to', ''),
        ];

        $filename = 'donors-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($filters): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Phone', 'Anonymous', 'Total Donated', 'Donations', 'Last Donation', 'Locale', 'Status']);

            $this->filteredQuery($filters)
                ->orderBy('id')
                ->chunk(200, function ($rows) use ($handle): void {
                    foreach ($rows as $donor) {
                        fputcsv($handle, [
                            $donor->id,
                            $donor->name,
                            $donor->email,
                            $donor->phone,
                            $donor->is_anonymous ? 'yes' : 'no',
                            $donor->total_donated,
                            $donor->donations_count,
                            $donor->last_donation_at?->toDateTimeString(),
                            $donor->locale,
                            $donor->status,
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * @param  array<string, string>  $filters
     * @return Builder<Donor>
     */
    private function filteredQuery(array $filters): Builder
    {
        return Donor::query()
            ->when($filters['status'] !== '', fn (Builder $q) => $q->where('status', $filters['status']))
            ->when($filters['min_donated'] !== '', fn (Builder $q) => $q->where('total_donated', '>=', (float) $filters['min_donated']))
            ->when($filters['max_donated'] !== '', fn (Builder $q) => $q->where('total_donated', '<=', (float) $filters['max_donated']))
            ->when($filters['date_from'] !== '', fn (Builder $q) => $q->whereDate('last_donation_at', '>=', $filters['date_from']))
            ->when($filters['date_to'] !== '', fn (Builder $q) => $q->whereDate('last_donation_at', '<=', $filters['date_to']))
            ->when($filters['search'] !== '', function (Builder $q) use ($filters): void {
                $term = '%'.$filters['search'].'%';
                $q->where(function (Builder $inner) use ($term): void {
                    $inner->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
            });
    }
}
