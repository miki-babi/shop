<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http; // Make sure this is at the top
use Illuminate\Support\Facades\Cache;


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
                    'SenderName' => 'EATH',
                    'SenderAddress' => 'Addis Ababa, Ethiopia',
                    'SenderPostcode' => '12345',
                    'SenderCity' => 'Addis Ababa',
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
                do {
                    $identifier = "EA" . mt_rand(10000000, 99999999) . "XET";
                } while (Order::where('identifier', $identifier)->exists());

                $order = Order::create([
                    'shop_id' => $storeId,
                    'order_id' => $order_id,
                    'unique_mailitem_id' => $data['unique_mailitem_id'] ?? '',
                    'identifier' => $identifier,
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
                $this->bookOrder($order->toArray(), $storeId);
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
    private function bookOrder($orderData, $storeId)
    {
        $body = [
            "ForceDuplicate" => "false",
            "Identifier" => $orderData['identifier'],
            "MailProductType" => "DomEP",
            "EventType" => "01",
            "Username" => "EASTAFRIAPI_USER",
            "Facility" => "ETADDA",
            "Timestamp" => now()->format('Y-m-d\TH:i:s.v O'),
            "MailItemAttributes" => [
                "Weight" => $orderData['Weight'] ?? "",
                "SenderName" => $orderData['SenderName'] ?? "",
                "SenderAddress" => $orderData['SenderAddress'] ?? "",
                "SenderPostcode" => $orderData['SenderPostcode'] ?? "",
                "SenderCity" => $orderData['SenderCity'] ?? "",
                "SenderPhone" => $orderData['SenderPhone'] ?? "",
                "SenderEmail" => $orderData['SenderEmail'] ?? "",
                "SenderPOBox" => $orderData['SenderPOBox'] ?? "",
                "RecipientName" => $orderData['RecipientName'] ?? "",
                "RecipientAddress" => $orderData['RecipientAddress'] ?? "",
                "RecipientPostcode" => $orderData['RecipientPostcode'] ?? "",
                "RecipientCity" => $orderData['RecipientCity'] ?? "",
                "RecipientPhone" => $orderData['RecipientPhone'] ?? "",
                "RecipientEmail" => $orderData['RecipientEmail'] ?? "",
                "RecipientPOBox" => $orderData['RecipientPOBox'] ?? "",
            ],
            "EventAttributes" => [
                "Condition" => "100",
            ]
        ];

        Log::info("Booking order for store $storeId", ['order_body' => $body]);

        // Example request, replace URL and headers as needed
$tokens = ['dps_token_1', 'dps_token_2'];
$response = null;

foreach ($tokens as $token) {
    $attempt = 0;
    $maxAttempts = 10; // optional: add limit to prevent infinite loops
    while (true) {
        $attempt++;

        try {
            $response = Http::timeout(30)->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . Cache::get($token),
            ])->post('https://dpstest.ethio.post:8200/external-api/mail-items', $body);

            if ($response->successful()) {
                Log::info("Order booked successfully using token $token", ['response' => $response->json()]);
                return response()->json([
                    'success' => true,
                    'message' => 'Order booked successfully.',
                    'response' => $response->json()
                ]);
            }

            // Unauthorized → log and break to try next token
            if ($response->status() === 401) {
                Log::warning("Token $token unauthorized", ['status' => $response->status(), 'body' => $response->body()]);
                break;
            }

            // Other failure → log and retry
            Log::error("Token $token failed (status: {$response->status()})", ['body' => $response->body()]);
        } catch (\Exception $e) {
            Log::error("Exception with token $token on attempt $attempt", ['error' => $e->getMessage()]);
        }

        // Optional: stop infinite retries
        if ($attempt >= $maxAttempts) {
            Log::warning("Giving up on token $token after $maxAttempts attempts");
            break;
        }

        sleep(2); // wait before retry
    }
}

return response()->json([
    'success' => false,
    'message' => 'Order booking failed using all tokens.'
], 500);


        Log::info("Booking response", ['response' => $response->json()]);

        return $response->json();
    }
}
