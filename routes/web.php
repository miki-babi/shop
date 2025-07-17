<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/', function () {

        $orders = \App\Models\Order::where('order_status', 'booked')->get();
        return view('welcome', compact('orders'));
});

Route::post('/webhook/{store}', [WebhookController::class, 'handle'])->withoutMiddleware([VerifyCsrfToken::class]);
