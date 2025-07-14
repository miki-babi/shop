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

        Log::debug("Extracted status: ", ['status' => $status]);
        Log::debug("Extracted order_id: ", ['order_id' => $order_id]);

        try {
            // Process based on status
            if ($status === 'shipment-ready') {
                Log::info("Processing shipment-ready for order", ['order_id' => $order_id, 'store' => $store]);
                $storeId = Shop::where('name', $store)->first()->id ?? null;
                Log::debug("Resolved storeId", ['storeId' => $storeId]);

                $order = Order::create([
                    'shop_id' => $storeId, // Assuming $store is the shop ID
                    'order_id' => $order_id,
                    'unique_mailitem_id' => $data['unique_mailitem_id'] ?? null,
                    'identifier' => $data['identifier'] ?? null,
                    'event' => $status,
                    // Add other fields as needed
                ]);
                Log::info("Order created", ['order_db_id' => $order->id]);
            }

            if ($status === 'completed') {
                Log::info("Webhook from $store: Order $order_id is $status");
            }
        } catch (\Exception $e) {
            Log::error("Error processing webhook", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }

        return response()->json(['received' => true]);
    }
}
