<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Page;
use App\Services\Settings\SettingsService;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        $pages = Page::query()
            ->withCount('sections')
            ->orderBy('slug')
            ->get();

        return view('admin.pages.index', compact('pages'));
    }

    public function show(Page $page): View
    {
        $page->load(['sections' => fn ($q) => $q->withCount('blocks')->orderBy('sort_order')]);

        $settings = null;
        $mediaLibrary = collect();

        if (in_array($page->slug, ['who-we-are', 'team', 'contact'], true)) {
            $settings = app(SettingsService::class)->all();
            $mediaLibrary = Media::query()->latest()->limit(200)->get();
        }

        return view('admin.pages.show', compact('page', 'settings', 'mediaLibrary'));
    }
}
