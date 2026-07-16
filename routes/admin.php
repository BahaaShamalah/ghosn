<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Donations\DonationController;
use App\Http\Controllers\Admin\Donations\DonationReceiptController;
use App\Http\Controllers\Admin\Pages\PageController;
use App\Http\Controllers\Admin\Pages\PageSectionBlockController;
use App\Http\Controllers\Admin\Pages\PageSectionController;
use App\Http\Controllers\Admin\Settings\SettingsController;
use App\Http\Controllers\Admin\Settings\SocialLinkController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin.permission'])
    ->group(function (): void {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('password', [\App\Http\Controllers\Admin\Profile\PasswordController::class, 'edit'])->name('password.edit');
        Route::put('password', [\App\Http\Controllers\Admin\Profile\PasswordController::class, 'update'])->name('password.update');

        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::get('settings/{group}', [SettingsController::class, 'show'])->name('settings.show');
        Route::put('settings/{group}', [SettingsController::class, 'updateGroup'])->name('settings.update.group');

        Route::post('settings/social/links', [SocialLinkController::class, 'store'])->name('settings.social.links.store');
        Route::put('settings/social/links/{socialLink}', [SocialLinkController::class, 'update'])->name('settings.social.links.update');
        Route::delete('settings/social/links/{socialLink}', [SocialLinkController::class, 'destroy'])->name('settings.social.links.destroy');
        Route::patch('settings/social/links/{socialLink}/toggle', [SocialLinkController::class, 'toggle'])->name('settings.social.links.toggle');
        Route::patch('settings/social/links/{socialLink}/move/{direction}', [SocialLinkController::class, 'move'])
            ->whereIn('direction', ['up', 'down'])
            ->name('settings.social.links.move');

        Route::get('pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('pages/{page}', [PageController::class, 'show'])->name('pages.show');

        Route::get('pages/{page}/sections/{section}/edit', [PageSectionController::class, 'edit'])->name('pages.sections.edit');
        Route::get('pages/{page}/sections/{section}/hero', [PageSectionController::class, 'editHero'])->name('pages.sections.hero.edit');
        Route::get('pages/{page}/sections/{section}/about', [PageSectionController::class, 'editAbout'])->name('pages.sections.about.edit');
        Route::get('pages/{page}/sections/{section}/volunteer', [PageSectionController::class, 'editVolunteer'])->name('pages.sections.volunteer.edit');
        Route::get('pages/{page}/sections/{section}/content', [PageSectionController::class, 'editContent'])->name('pages.sections.content.edit');
        Route::put('pages/{page}/sections/{section}', [PageSectionController::class, 'update'])->name('pages.sections.update');
        Route::put('pages/{page}/sections/{section}/hero', [PageSectionController::class, 'updateHero'])->name('pages.sections.hero.update');
        Route::put('pages/{page}/sections/{section}/about', [PageSectionController::class, 'updateAbout'])->name('pages.sections.about.update');
        Route::put('pages/{page}/sections/{section}/volunteer', [PageSectionController::class, 'updateVolunteer'])->name('pages.sections.volunteer.update');
        Route::put('pages/{page}/sections/{section}/content', [PageSectionController::class, 'updateContent'])->name('pages.sections.content.update');
        Route::patch('pages/{page}/sections/{section}/reorder', [PageSectionController::class, 'reorder'])->name('pages.sections.reorder');

        Route::put('pages/{page}/sections/{section}/blocks/{block}', [PageSectionBlockController::class, 'update'])->name('pages.blocks.update');
        Route::patch('pages/{page}/sections/{section}/blocks/{block}/reorder', [PageSectionBlockController::class, 'reorder'])->name('pages.blocks.reorder');

        Route::get('donations', [DonationController::class, 'index'])->name('donations.index');
        Route::get('donations/{donation}/receipt', [DonationReceiptController::class, 'show'])->name('donations.receipt.show');
        Route::get('donations/{donation}/receipt/print', [DonationReceiptController::class, 'print'])->name('donations.receipt.print');
        Route::get('donations/{donation}/receipt/download', [DonationReceiptController::class, 'download'])->name('donations.receipt.download');
        Route::patch('donations/{donation}/confirm', [DonationController::class, 'confirm'])->name('donations.confirm');

        Route::get('donors/export', [\App\Http\Controllers\Admin\Donors\DonorController::class, 'export'])->name('donors.export');
        Route::get('donors', [\App\Http\Controllers\Admin\Donors\DonorController::class, 'index'])->name('donors.index');
        Route::get('donors/{donor}', [\App\Http\Controllers\Admin\Donors\DonorController::class, 'show'])->name('donors.show');
        Route::patch('donors/{donor}/toggle-block', [\App\Http\Controllers\Admin\Donors\DonorController::class, 'toggleBlock'])->name('donors.toggle-block');
        Route::post('donors/{donor}/send-email', [\App\Http\Controllers\Admin\Donors\DonorController::class, 'sendEmail'])->name('donors.send-email');

        Route::get('volunteers', [\App\Http\Controllers\Admin\VolunteerApplicationController::class, 'index'])->name('volunteers.index');
        Route::patch('volunteers/{application}', [\App\Http\Controllers\Admin\VolunteerApplicationController::class, 'updateStatus'])->name('volunteers.update-status');

        Route::get('messages', [\App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('messages.show');
        Route::patch('messages/{message}/read', [\App\Http\Controllers\Admin\ContactMessageController::class, 'markRead'])->name('messages.read');
        Route::delete('messages/{message}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('messages.destroy');

        Route::get('newsletter', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'index'])->name('newsletter.index');
        Route::delete('newsletter/{subscriber}', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'destroy'])->name('newsletter.destroy');

        Route::resource('users', \App\Http\Controllers\Admin\Users\UserController::class)->except(['show']);
        Route::resource('roles', \App\Http\Controllers\Admin\Users\RoleController::class)->except(['show']);

        require __DIR__.'/admin-campaigns.php';
        require __DIR__.'/admin-cms.php';
        require __DIR__.'/admin-media.php';
    });
