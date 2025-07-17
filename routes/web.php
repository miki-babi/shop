<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Http\Controllers\SessionController;
// use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth ;


Route::get('/barcode/{code}', function ($code) {
    $generator = new BarcodeGeneratorPNG();
    $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128);

    return response($barcode)
        ->header('Content-Type', 'image/png');
});

Route::get('/', function () {

        $orders = \App\Models\Order::where('order_status', 'booked')->get();
        return view('welcome', compact('orders'));
});




Route::get('/',  [SessionController::class, 'showLoginForm']);
Route::get('/login', [SessionController::class, 'showLoginForm'])->name('login');
Route::post('/login', [SessionController::class, 'login']);
Route::post('/logout', [SessionController::class, 'logout'])->name('logout');
// Route::get('/dashboard', function () {
//     $deliveries = Delivery::where('farmer_id', Auth::id())->get();
//     return view('dashboard' , compact('deliveries'));
// })->name('dashboard');

Route::get('/dashboard', function () {

    if (!Auth::check()) {
        return redirect()->route('login');
    }
   $orders = \App\Models\Order::where('order_status', 'booked')->get();
    return view('dashboard', compact('orders'));
})->name('dashboard');
Route::get('/dashboard/{id}', function ($id) {


    if (!Auth::check()) {
        return redirect()->route('login');
    }
   $order = \App\Models\Order::where('order_status', 'booked')->
   where('id',$id)->with('shop')
   ->first();;
   dd($order);
    return view('order.index', compact('order'));
})->name('detail');

Route::post('/webhook/{store}', [WebhookController::class, 'handle'])->withoutMiddleware([VerifyCsrfToken::class]);
