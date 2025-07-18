<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RefreshDpsToken extends Command
{
    protected $signature = 'refresh:dpstoken';
    protected $description = 'Refresh and cache DPS access token';

     public function handle()
    {
        $url = 'https://dpstest.ethio.post:8200/identity/connect/token?returnUserInfo=true';

        $formParams = [
            'client_id' => 'External',
            'grant_type' => 'password',
            'username' => 'EASTAFRIAPI_USER',
            'password' => 'Besh@Test1',
        ];

        for ($i = 1; $i <= 2; $i++) {
            if ($i === 2) {
                sleep(3); // Wait 3 seconds before requesting the second token
            }
            $success = false;
            $attempt = 0;
            while (!$success) {
                try {
                    $response = Http::timeout(30)->asForm()
                        ->withOptions(['verify' => false])
                        ->post($url, $formParams);

                    $data = $response->json();

                    if (isset($data['access_token'], $data['expires_in'])) {
                        $ttl = max(30, $data['expires_in'] - 5); // store slightly less than actual expiry
                        Cache::put('dps_token_' . $i, $data['access_token'], $ttl);
                        // Log::channel('dps_token')->info('DPS token ' . $i . ':', [
                        //     'token' => Cache::get('dps_token_' . $i),
                        //     'ttl' => $ttl,
                        //     'timestamp' => now()->toDateTimeString(),
                        // ]);
                        $this->info('Token ' . $i . ' cached.');
                        $success = true;
                    } else {
                        Log::channel('dps_token')->warning('DPS token request succeeded but no token returned', $data);
                        $this->warn('No token in response for token ' . $i . '.');
                        break; // Stop retrying if response is successful but no token
                    }
                } catch (\Exception $e) {
                    $attempt++;
                    if (strpos($e->getMessage(), 'timed out') === false) {
                        Log::channel('dps_token')->error('DPS token request failed', [
                            'error' => $e->getMessage(),
                            'attempt' => $attempt,
                            'token' => $i
                        ]);
                    }
                    $this->error('Request for token ' . $i . ' failed on attempt ' . $attempt . '. Retrying...');
                    
                }
            }
        }
    }
}
