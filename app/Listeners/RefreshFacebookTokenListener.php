<?php

namespace App\Listeners;

use TCG\Voyager\Events\SettingUpdated;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use TCG\Voyager\Models\Setting;

class RefreshFacebookTokenListener
{
    public function handle($event)
    {
        if ($event instanceof SettingUpdated) {
            $key = $event->setting->key;
            $value = $event->setting->value;

            if ($key === 'admin.whatsapp_access_token') {
                try {
                    $longLivedToken = $this->refreshFacebookToken($value);

                    $event->setting->value = $longLivedToken;
                    $event->setting->saveQuietly();

                    Log::info("✅ Facebook token refreshed successfully", [
                        'new_token' => $longLivedToken,
                    ]);
                } catch (\Exception $e) {
                    Log::error("Failed to refresh Facebook token: " . $e->getMessage());
                }
            }
        }
    }

    private function refreshFacebookToken(string $shortLivedToken): string
    {
        $whatsapp_client_id     = setting('admin.whatsapp_client_id');
        $whatsapp_client_secret = setting('admin.whatsapp_client_secret');

        $response = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [
            'grant_type'        => 'fb_exchange_token',
            'client_id'         => $whatsapp_client_id,
            'client_secret'     => $whatsapp_client_secret,
            'fb_exchange_token' => $shortLivedToken,
        ]);

        if (!$response->successful()) {
            throw new \Exception("Facebook API error: " . $response->body());
        }

        return $response->json()['access_token'];
    }
}
