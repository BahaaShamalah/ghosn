<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\BuilderReactPayload;
use App\Support\LegalPageContent;
use Illuminate\View\View;

class LegalPageController extends Controller
{
    public function show(string $slug): View
    {
        $key = LegalPageContent::keyForSlug($slug);
        abort_unless($key !== null, 404);

        $legal = LegalPageContent::forReact($key);
        $title = $legal['page']['title'][app()->getLocale()] ?? $legal['page']['title']['en'] ?? 'Policy';

        return view('public.builder.react', [
            'documentTitle' => $title,
            'landingPayload' => array_merge(BuilderReactPayload::sharedChrome(), [
                'pageType' => 'legal',
                'legal' => $legal,
            ]),
        ]);
    }
}
