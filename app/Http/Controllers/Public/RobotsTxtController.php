<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\RobotsTxtBuilder;
use App\Support\SitemapBuilder;
use Illuminate\Http\Response;

class RobotsTxtController extends Controller
{
    public function __invoke(): Response
    {
        return response(RobotsTxtBuilder::render(), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
