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

        Log::info("Webhook from $store: Order $order_id is $status");

        // Process based on status
        if ($status === 'completed') {
            // Run your logic here (e.g., notify, update DB, etc.)
        }

        return response()->json(['received' => true]);
    }
}
