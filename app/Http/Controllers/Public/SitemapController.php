<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\SitemapBuilder;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        return response(SitemapBuilder::render(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
