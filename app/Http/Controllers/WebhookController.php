<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    //
    public function handle(Request $request, $store)
    {
        $data = $request->all();
        $status = $data['status'] ?? null;
        $order_id = $data['id'] ?? null;
        
        Log::info("Webhook from $store: Received payload", $data);

        // Process based on status
        if ($status === 'shipment-ready') {
            // Run your logic here (e.g., notify, update DB, etc.)
        // Log::info("Webhook from $store: Order $order_id is $status");
        $storeId= Shop::where('name', $store)->first()->id ?? null;

        Order::create([
            'shop_id' => $storeId, // Assuming $store is the shop ID
            'order_id' => $order_id,
            'unique_mailitem_id' => $data['unique_mailitem_id'] ?? null,
            'identifier' => $data['identifier'] ?? null,
            'event' => $status,
            
        ]);

        }


        // Process based on status
        if ($status === 'completed') {
            // Run your logic here (e.g., notify, update DB, etc.)
        Log::info("Webhook from $store: Order $order_id is $status");

        }

        return response()->json(['received' => true]);
    }
}
