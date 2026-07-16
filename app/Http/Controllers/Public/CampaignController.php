<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $categorySlug = (string) $request->query('category', '');

        $campaigns = Campaign::query()
            ->public()
            ->with(['category', 'featuredImage'])
            ->when($categorySlug !== '', fn ($q) => $q->whereHas('category', fn ($cat) => $cat->where('slug', $categorySlug)))
            ->when($search !== '', function ($q) use ($search): void {
                $term = '%'.$search.'%';
                $q->where(function ($inner) use ($term): void {
                    $inner->where('title_en', 'like', $term)
                        ->orWhere('title_ar', 'like', $term)
                        ->orWhere('excerpt_en', 'like', $term)
                        ->orWhere('excerpt_ar', 'like', $term);
                });
            })
            ->ordered()
            ->paginate(9)
            ->withQueryString();

        return view('public.campaigns.index', [
            'campaigns' => $campaigns,
            'categories' => Category::query()->where('type', Category::TYPE_CAMPAIGN)->whereHas('campaigns', fn ($q) => $q->public())->orderBy('name_en')->get(),
            'search' => $search,
            'activeCategory' => $categorySlug,
        ]);
    }

    public function show(string $slug): View
    {
        $campaign = Campaign::query()
            ->public()
            ->with(['category', 'featuredImage', 'video'])
            ->where('slug', $slug)
            ->firstOrFail();

        $recentDonations = Donation::query()
            ->paid()
            ->where('campaign_id', $campaign->id)
            ->latest('paid_at')
            ->limit(10)
            ->get();

        $relatedCampaigns = Campaign::query()
            ->public()
            ->where('id', '!=', $campaign->id)
            ->with(['category', 'featuredImage'])
            ->ordered()
            ->limit(3)
            ->get();

        return view('public.campaigns.show', [
            'campaign' => $campaign,
            'galleryImages' => $campaign->mediaGallery(),
            'recentDonations' => $recentDonations,
            'relatedCampaigns' => $relatedCampaigns,
        ]);
    }
}
