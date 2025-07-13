<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/webhook/{store}', [WebhookController::class, 'handle'])->withoutMiddleware([VerifyCsrfToken::class]);
