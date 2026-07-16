<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\ContactReactPayload;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('public.builder.react', [
            'documentTitle' => app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact',
            'landingPayload' => ContactReactPayload::build(),
        ]);
    }
}
