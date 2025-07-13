<?php

namespace App\Http\Controllers;

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
        if ($status === null || $order_id === null) {
            Log::warning("Webhook from $store: Missing status or order_id");
            return response()->json(['error' => 'Invalid payload'], 400);
        }



        // Process based on status
        if ($status === 'completed') {
            // Run your logic here (e.g., notify, update DB, etc.)
        Log::info("Webhook from $store: Order $order_id is $status");

        }

        return response()->json(['received' => true]);
    }
}
