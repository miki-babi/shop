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

    // public function handle()
    // {
    //     $url = 'https://dpstest.ethio.post:8200/identity/connect/token?returnUserInfo=true';

    //     $formParams = [
    //         'client_id' => 'External',
    //         'grant_type' => 'password',
    //         'username' => 'EASTAFRIAPI_USER',
    //         'password' => 'Besh@Test1',
    //     ];

    //     try {
    //         $response = Http::timeout(30)->asForm()
    //             ->withOptions(['verify' => false])
    //             ->post($url, $formParams);

    //         $data = $response->json();

    //         if (isset($data['access_token'], $data['expires_in'])) {
    //             $ttl = max(60, $data['expires_in'] - 10); // store slightly less
    //             Cache::put('dps_token', $data['access_token'], $ttl);
    //             $this->info('Token cached.');
    //         } else {
    //             $this->error('Token not received.');
    //         }
    //     } catch (\Exception $e) {
    //         $this->error('Token request failed: ' . $e->getMessage());
    //     }
    // }
     public function handle()
    {
        $url = 'https://dpstest.ethio.post:8200/identity/connect/token?returnUserInfo=true';

        $formParams = [
            'client_id' => 'External',
            'grant_type' => 'password',
            'username' => 'EASTAFRIAPI_USER',
            'password' => 'Besh@Test1',
        ];

        $maxAttempts = 2;
        $attempt = 0;
        $success = false;
        $lastException = null;

        while ($attempt < $maxAttempts && !$success) {
            try {
                $response = Http::timeout(30)->asForm()
                    ->withOptions(['verify' => false])
                    ->post($url, $formParams);

                $data = $response->json();

                if (isset($data['access_token'])) {
                    Log::info('DPS token:', ['token' => $data['access_token']]);
                    $this->info('Token logged.');
                    $success = true;
                } else {
                    Log::warning('DPS token request succeeded but no token returned', $data);
                    $this->warn('No token in response.');
                    break; // No point retrying if response is successful but no token
                }
            } catch (\Exception $e) {
                $lastException = $e;
                Log::error('DPS token request failed', ['error' => $e->getMessage(), 'attempt' => $attempt + 1]);
                $this->error('Request failed on attempt ' . ($attempt + 1) . '.');
                $attempt++;
                if ($attempt < $maxAttempts) {
                    // Optionally, add a short delay before retrying
                    sleep(1);
                }
            }
        }

        if (!$success && $lastException) {
            $this->error('All attempts to get DPS token failed.');
        }
    }
}
