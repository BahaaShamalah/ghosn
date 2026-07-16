<?php

namespace App\Http\Controllers\Admin\Campaigns;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Campaigns\StoreCampaignRequest;
use App\Http\Requests\Admin\Campaigns\UpdateCampaignRequest;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Media;
use App\Support\CmsSlug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'status' => (string) $request->query('status', ''),
            'search' => trim((string) $request->query('search', '')),
        ];

        $campaigns = Campaign::query()
            ->with(['category', 'featuredImage'])
            ->when($filters['status'] !== '', fn ($q) => $q->where('status', $filters['status']))
            ->when($filters['search'] !== '', function ($q) use ($filters): void {
                $term = '%'.$filters['search'].'%';
                $q->where(function ($inner) use ($term): void {
                    $inner->where('title_en', 'like', $term)
                        ->orWhere('title_ar', 'like', $term)
                        ->orWhere('slug', 'like', $term);
                });
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('admin.campaigns.index', [
            'campaigns' => $campaigns,
            'filters' => $filters,
            'statuses' => config('campaigns.statuses', []),
        ]);
    }

    public function create(): View
    {
        return view('admin.campaigns.create', $this->formData());
    }

    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = CmsSlug::uniqueFrom($data['title_en'], new Campaign);
        }

        if (empty($data['starts_at']) && $data['status'] === Campaign::STATUS_ACTIVE) {
            $data['starts_at'] = now();
        }

        $campaign = Campaign::query()->create($data);

        return redirect()
            ->route('admin.campaigns.edit', $campaign)
            ->with('status', __('admin.campaigns.created'));
    }

    public function edit(Campaign $campaign): View
    {
        return view('admin.campaigns.edit', array_merge($this->formData(), [
            'campaign' => $campaign->load(['category', 'featuredImage', 'video']),
        ]));
    }

    public function update(UpdateCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        $campaign->update($request->validated());

        return back()->with('status', __('admin.campaigns.updated'));
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $campaign->delete();

        return redirect()
            ->route('admin.campaigns.index')
            ->with('status', __('admin.campaigns.deleted'));
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'categories' => Category::query()->where('type', Category::TYPE_CAMPAIGN)->orderBy('name_en')->get(),
            'mediaLibrary' => Media::query()->latest()->limit(200)->get(),
            'statuses' => config('campaigns.statuses', []),
            'currencies' => array_keys(config('donations.currencies', ['USD' => []])),
        ];
    }
}
