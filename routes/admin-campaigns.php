<?php

use App\Http\Controllers\Admin\Campaigns\CampaignController;
use Illuminate\Support\Facades\Route;

Route::resource('campaigns', CampaignController::class)->except(['show']);
