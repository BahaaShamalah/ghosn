<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class OfficialPageLegacyRedirectController extends Controller
{
    public function redirect(string $slug): RedirectResponse
    {
        return redirect()->route('pages.show', ['slug' => $slug], 301);
    }
}
