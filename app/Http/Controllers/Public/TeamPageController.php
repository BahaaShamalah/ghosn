<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\BuilderReactPayload;
use App\Support\TeamPageContent;
use Illuminate\View\View;

class TeamPageController extends Controller
{
    public function index(): View
    {
        $team = TeamPageContent::forReact();
        $locale = app()->getLocale();

        return view('public.builder.react', [
            'documentTitle' => $team['hero']['title'][$locale] ?? $team['hero']['title']['en'] ?? 'Our Team',
            'landingPayload' => array_merge(BuilderReactPayload::sharedChrome(), [
                'pageType' => 'team',
                'team' => $team,
            ]),
        ]);
    }
}
