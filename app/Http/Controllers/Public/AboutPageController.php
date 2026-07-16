<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\AboutPageContent;
use App\Support\BuilderReactPayload;
use Illuminate\View\View;

class AboutPageController extends Controller
{
    public function index(): View
    {
        $about = AboutPageContent::forReact();
        $locale = app()->getLocale();

        return view('public.builder.react', [
            'documentTitle' => $about['hero']['title'][$locale]
                ?? $about['hero']['title']['en']
                ?? 'About',
            'landingPayload' => array_merge(BuilderReactPayload::sharedChrome(), [
                'pageType' => 'about',
                'aboutPage' => $about,
            ]),
        ]);
    }
}
