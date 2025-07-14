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

                // Hardcoded sender details
                $sender = [
                    'SenderName' => 'Your Company Name',
                    'SenderAddress' => 'Your Address',
                    'SenderPostcode' => '12345',
                    'SenderCity' => 'Your City',
                    'SenderPhone' => '1234567890',
                    'SenderEmail' => 'sender@example.com',
                    'SenderPOBox' => 'PO123'
                ];

                // Extract recipient details from WooCommerce webhook, fallback to billing if shipping is missing
                $recipient = [
                    'RecipientName' => $data['shipping']['first_name'] ?? $data['billing']['first_name'] ?? '',
                    'RecipientAddress' => $data['shipping']['address_1'] ?? $data['billing']['address_1'] ?? '',
                    'RecipientPostcode' => $data['shipping']['postcode'] ?? $data['billing']['postcode'] ?? '',
                    'RecipientCity' => $data['shipping']['city'] ?? $data['billing']['city'] ?? '',
                    'RecipientPhone' => $data['shipping']['phone'] ?? $data['billing']['phone'] ?? '',
                    'RecipientEmail' => $data['shipping']['email'] ?? $data['billing']['email'] ?? '',
                    'RecipientPOBox' => $data['shipping']['po_box'] ?? $data['billing']['po_box'] ?? '',
                ];

                $order = Order::create([
                    'shop_id' => $storeId,
                    'order_id' => $order_id,
                    'unique_mailitem_id' => $data['unique_mailitem_id'] ?? '',
                    'identifier' => $data['identifier'] ?? '',
                    'event' => $status,
                    'ForceDuplicate' => 'false',
                    'MailProductType' => $data['mail_product_type'] ?? '',
                    'EventType' => $data['event_type'] ?? '',
                    'Username' => $data['username'] ?? '',
                    'Facility' => $data['facility'] ?? '',
                    'Timestamp' => $data['timestamp'] ?? '',
                    'Weight' => $data['weight'] ?? '',
                    'Condition' => $data['condition'] ?? '',
                    // Sender fields
                    'SenderName' => $sender['SenderName'],
                    'SenderAddress' => $sender['SenderAddress'],
                    'SenderPostcode' => $sender['SenderPostcode'],
                    'SenderCity' => $sender['SenderCity'],
                    'SenderPhone' => $sender['SenderPhone'],
                    'SenderEmail' => $sender['SenderEmail'],
                    'SenderPOBox' => $sender['SenderPOBox'],
                    // Recipient fields
                    'RecipientName' => $recipient['RecipientName'],
                    'RecipientAddress' => $recipient['RecipientAddress'],
                    'RecipientPostcode' => $recipient['RecipientPostcode'],
                    'RecipientCity' => $recipient['RecipientCity'],
                    'RecipientPhone' => $recipient['RecipientPhone'],
                    'RecipientEmail' => $recipient['RecipientEmail'],
                    'RecipientPOBox' => $recipient['RecipientPOBox'],
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
