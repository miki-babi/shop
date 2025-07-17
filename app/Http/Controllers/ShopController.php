<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopController extends Controller
{
    //
    public function index()
    {
        // Logic to list all shops
        $orders = \App\Models\Order::where('order_status', 'booked')->get();
        return view('welcome', compact('orders'));
    }
}
