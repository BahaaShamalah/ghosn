<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\ReorderSocialLinksRequest;
use App\Http\Requests\Admin\Settings\StoreSocialLinkRequest;
use App\Http\Requests\Admin\Settings\UpdateSocialLinkRequest;
use App\Models\SocialLink;
use Illuminate\Http\RedirectResponse;

class SocialLinkController extends Controller
{
    public function store(StoreSocialLinkRequest $request): RedirectResponse
    {
        $maxSort = (int) SocialLink::query()->max('sort_order');

        SocialLink::query()->create([
            ...$request->validated(),
            'sort_order' => $maxSort + 1,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('status', __('admin.settings.social_link_created'));
    }

    public function update(UpdateSocialLinkRequest $request, SocialLink $socialLink): RedirectResponse
    {
        $socialLink->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', __('admin.settings.social_link_updated'));
    }

    public function destroy(SocialLink $socialLink): RedirectResponse
    {
        $socialLink->delete();

        return back()->with('status', __('admin.settings.social_link_deleted'));
    }

    public function toggle(SocialLink $socialLink): RedirectResponse
    {
        $socialLink->update(['is_active' => ! $socialLink->is_active]);

        return back()->with('status', __('admin.settings.social_link_updated'));
    }

    public function reorder(ReorderSocialLinksRequest $request): RedirectResponse
    {
        foreach ($request->validated('order') as $index => $id) {
            SocialLink::query()->whereKey($id)->update(['sort_order' => $index]);
        }

        return back()->with('status', __('admin.settings.social_links_reordered'));
    }

    public function move(SocialLink $socialLink, string $direction): RedirectResponse
    {
        $links = SocialLink::query()->ordered()->get()->values();
        $index = $links->search(fn (SocialLink $link): bool => $link->id === $socialLink->id);

        if ($index === false) {
            return back();
        }

        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($target < 0 || $target >= $links->count()) {
            return back();
        }

        $current = $links[$index];
        $swap = $links[$target];

        $currentOrder = $current->sort_order;
        $current->update(['sort_order' => $swap->sort_order]);
        $swap->update(['sort_order' => $currentOrder]);

        return back()->with('status', __('admin.settings.social_links_reordered'));
    }
}
