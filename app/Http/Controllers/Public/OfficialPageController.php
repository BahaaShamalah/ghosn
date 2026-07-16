<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContentPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OfficialPageController extends Controller
{
    public function show(string $slug): View|RedirectResponse
    {
        if ($slug === 'about') {
            return redirect()->route('about');
        }

        $page = ContentPage::query()
            ->published()
            ->with('featuredImage')
            ->where('slug', $slug)
            ->first();

        if ($page) {
            return view('public.pages.show', [
                'page' => $page,
                'preview' => false,
            ]);
        }

        $pages = config('static-pages', []);

        abort_unless(array_key_exists($slug, $pages), 404);

        return view('public.pages.static', [
            'slug' => $slug,
            'page' => $pages[$slug],
        ]);
    }
}
