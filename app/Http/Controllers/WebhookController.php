<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $url = "https://graph.facebook.com/v22.0/710559555474756/messages";

        $token = "EAAF6WZAWiLfQBPCTazpqCos4vUxcDSU85okct8HN6M7QltZC3CGBONJjthNBuaoWme9vIStnqxCMbPPW7kNsZAo8KZCDMv58AtQreA3YH8kYTT0ODTwyzF7kw5HSBoTcTaooQaX7QeCjCqYZAWEQv9e4k54dw1BNzGHMhrYyt1IqSGXnNMZAq3Ndl1FxBSOuKzZCgftfxqf3eT4hnkZC8OMPB9KKrX8AEBlozcZARD9mRhrJZCbvzdPhV67WrTfBEwk5QZD";

        $data = [
            "messaging_product" => "whatsapp",
            "to" => "918007647931", // must include country code
            "type" => "template",
            "template" => [
                "name" => "hello_world",
                "language" => ["code" => "en_US"]
            ]
        ];

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$token}",
                "Content-Type: application/json"
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true, // verify SSL
            CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE)
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return response()->json(['error' => curl_error($ch)], 500);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Log or debug raw response
        \Log::info("WhatsApp API response: " . $response);

        return response()->json([
            'http_code' => $httpCode,
            'response' => json_decode($response, true) ?: $response
        ]);
    }
}
