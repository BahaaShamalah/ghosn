<?php



use App\Http\Controllers\Public\DonateController;

use App\Http\Controllers\Public\PayPalDonationController;

use App\Http\Controllers\Public\HomeController;

use App\Http\Controllers\Public\LocaleController;

use App\Http\Controllers\Public\OfficialPageLegacyRedirectController;

use Illuminate\Support\Facades\Route;



Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/robots.txt', \App\Http\Controllers\Public\RobotsTxtController::class)->name('robots');
Route::get('/sitemap.xml', \App\Http\Controllers\Public\SitemapController::class)->name('sitemap');

Route::get('/volunteer', [\App\Http\Controllers\Public\BuilderPageController::class, 'volunteer'])->name('volunteer');
Route::get('/about', [\App\Http\Controllers\Public\AboutPageController::class, 'index'])->name('about');
Route::get('/our-team', [\App\Http\Controllers\Public\TeamPageController::class, 'index'])->name('team');
Route::get('/contact', [\App\Http\Controllers\Public\ContactController::class, 'index'])->name('contact');
Route::redirect('/join-us', '/volunteer', 301);

foreach (config('legal-pages.slugs', []) as $slug) {
    Route::get('/'.$slug, fn () => app(\App\Http\Controllers\Public\LegalPageController::class)->show($slug));
}



Route::get('/news', [\App\Http\Controllers\Public\NewsController::class, 'index'])->name('news.index');

Route::get('/news/{slug}', [\App\Http\Controllers\Public\NewsController::class, 'show'])->name('news.show');



Route::get('/campaigns', [\App\Http\Controllers\Public\CampaignController::class, 'index'])->name('campaigns.index');

Route::get('/campaigns/{slug}', [\App\Http\Controllers\Public\CampaignController::class, 'show'])->name('campaigns.show');



Route::get('/donate', [DonateController::class, 'index'])->name('donate');

Route::post('/donate', [DonateController::class, 'store'])

    ->middleware('throttle:10,1')

    ->name('donate.store');

Route::post('/donate/paypal/create-order', [PayPalDonationController::class, 'createOrder'])

    ->middleware('throttle:10,1')

    ->name('donate.paypal.create-order');

Route::post('/donate/paypal/capture-order', [PayPalDonationController::class, 'captureOrder'])

    ->middleware('throttle:10,1')

    ->name('donate.paypal.capture-order');

Route::get('/donate/success', [DonateController::class, 'success'])->name('donate.success');

Route::get('/donate/complete/{reference}', [DonateController::class, 'complete'])->name('donate.complete');

Route::get('/donate/thank-you/{reference}', [DonateController::class, 'thankYou'])->name('donate.thank-you');

Route::get('/donate/cancel/{reference}', [DonateController::class, 'cancel'])->name('donate.cancel');



Route::get('/locale/{locale}', [LocaleController::class, 'switch'])

    ->name('locale.switch')

    ->where('locale', implode('|', config('locale.supported', ['en', 'ar'])));

Route::post('/volunteer-applications', [\App\Http\Controllers\Public\VolunteerApplicationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('volunteer-applications.store');
Route::post('/contact-messages', [\App\Http\Controllers\Public\ContactMessageController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('contact-messages.store');
Route::post('/newsletter-subscriptions', [\App\Http\Controllers\Public\NewsletterSubscriptionController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('newsletter-subscriptions.store');



Route::get('/pages/{slug}', [OfficialPageLegacyRedirectController::class, 'redirect'])

    ->name('pages.legacy')

    ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*');

