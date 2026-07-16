<?php



namespace App\Http\Controllers\Public;



use App\Http\Controllers\Controller;

use App\Support\BuilderPageContent;

use App\Support\BuilderReactPayload;

use Illuminate\View\View;



class BuilderPageController extends Controller

{

    public function volunteer(): View

    {

        $page = BuilderPageContent::findPage('volunteer');

        abort_unless($page, 404);



        return view('public.builder.react', [

            'documentTitle' => $page->meta_title_en ?: $page->title_en,

            'landingPayload' => BuilderReactPayload::volunteer($page),

        ]);

    }



    public function whoWeAre(): View

    {

        $page = BuilderPageContent::findPage('who-we-are');

        abort_unless($page, 404);



        return view('public.builder.react', [

            'documentTitle' => $page->meta_title_en ?: $page->title_en,

            'landingPayload' => BuilderReactPayload::whoWeAre($page),

        ]);

    }

}

