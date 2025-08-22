<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\PhotoApprovals;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Models\Complaint;
use Illuminate\Support\Facades\Http;
class DashboardController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    private $verifyToken = "railway";
    private $whatsappToken = "EAAF6WZAWiLfQBPGYt4KaKvvYOZA6423zb5OlmJh3g3ZCjnYxBwLTzG7jb0gZBp7yqkcCtB4zIHdAVGZAsPZAGdAKVEJqQlMgVPGHI4ZAZCoCN7r9lL7yZBV5HEZBrPwiZBsiTNIUamCv1ccGWDWl6S89avZBGi9XZBTxj6GW7c76Ui4KDYfCeWGb2g2ZAPypg5doEMsaG9HIrj4mUqbrtPl15Y";
    private $phoneNumberId = "710559555474756";
    public function verifyWebhook(Request $request)
    {

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode && $token) {
            if ($mode === "subscribe" && $token === $this->verifyToken) {
                return response($challenge, 200);
            } else {
                return response("Forbidden", 403);
            }
        }
        return response("Invalid", 400);
    }
    public function handleWebhook(Request $request)
{
    try {
        $entry = $request->input('entry.0');
        $changes = $entry['changes'][0] ?? null;
        $message = $changes['value']['messages'][0] ?? null;

        if ($message) {
            $from = $message['from'];
            $text = trim($message['text']['body'] ?? "");

            $complaint = Complaint::where('fir_number', $text)->first();

            if ($complaint) {
                $this->sendWhatsAppMessage(
                    $from,
                    "User",
                    $complaint->fir_number,
                    now()->toDateString(),
                    "Police Station",
                    "Officer Name",
                    $complaint
                );
            } else {
                $cacheKey = "fir_attempts_" . $from;
                $attempts = cache()->get($cacheKey, 0);

                if ($attempts >= 3) {
                    $this->sendRawWhatsAppMessage(
                        $from,
                        "You have exceeded the maximum number of attempts (3) in 24 hours. Please try again tomorrow."
                    );
                } else {
                    $attempts++;
                    cache()->put($cacheKey, $attempts, now()->addDay());

                    $this->sendRawWhatsAppMessage(
                        $from,
                        "Your FIR number was not found. Please try again with the correct FIR number.\n\n" .
                        "Example: FIR2024-123456\n\n" .
                        "Attempts used: {$attempts}/3"
                    );
                }
            }
        }

        return response()->json(['status' => 'ok'], 200);
    } catch (\Exception $e) {
        \Log::error("Webhook error: " . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
}

    private function sendWhatsAppMessage($to, $complainantName, $firNumber, $date, $policeStation, $officerName, $complaint)
    {
        $url = "https://graph.facebook.com/v23.0/{$this->phoneNumberId}/messages";
        if ($complaint === '') {
            $whatsappMessages = 'Your fir number not found please try agin with correct FIR number';
        } else {
            $status =$complaint->status ?? '';
            $complainant_name =$complaint->complainant_name ?? '';
            $fir_number =$complaint->fir_number ?? '';
            $fir_date =$complaint->fir_date ?? '';
            $police_station_name =$complaint->police_station_name ?? '';
            $officer_name =$complaint->officer_name ?? '';
            $police_station_number =$complaint->police_station_number ?? '';
            $whatsappMessages = Helper::getMessage($status, $complainant_name, $fir_number, $fir_date, $police_station_name, $officer_name, $police_station_number);
        }
        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "text",
            "text" => [
                "body" => $whatsappMessages[0]
            ],
        ];
        $response = \Http::withOptions(['verify' => false])
    ->withToken($this->whatsappToken)
    ->post($url, $payload);

        \Log::info("Sent bilingual message:", $response->json());
        return $response->json();
    }

    private function sendRawWhatsAppMessage($to, $message)
{
    $url = "https://graph.facebook.com/v23.0/{$this->phoneNumberId}/messages";

    $payload = [
        "messaging_product" => "whatsapp",
        "to" => $to,
        "type" => "text",
        "text" => [
            "body" => $message
        ],
    ];

    $response = \Http::withOptions(['verify' => false])->withToken($this->whatsappToken)->post($url, $payload);

    \Log::info("Sent simple WhatsApp message:", $response->json());
    return $response->json();
}

    // public function dashboard()
    // {
    //     $user = Auth::user();
    //     if(!$user){
    //         return Voyager::view('voyager::login');
    //     }
    //     // $total_declined  = $total_approved = 0;

    //     // $total_declined  = Helper::get_photodata_by_status(0);
    //     // $total_approved  = Helper::get_photodata_by_status(1);

    //     //  $recent_gen_photo = PhotoApprovals::orderBy('id', 'DESC')->limit(5)->get()->toArray();

    //     // $data = [
    //     //     'total_declined'    => $total_declined,
    //     //     'total_approved'    => $total_approved,
    //     //     'recent_gen_photo'  => $recent_gen_photo,
    //     // ];

    //    // return Voyager::view('voyager::index', compact('data'));
    //     return Voyager::view('voyager::index');
    // }

    public function refreshFacebookToken()
{
    try {

        $clientId = "416000157822452"; 
        $clientSecret = "ce811510f9c4ffbb6f2cf8cd4e63b1bb";

        $url = "https://graph.facebook.com/v23.0/oauth/access_token";

        $response = Http::withoutVerifying()->get($url, [
    'grant_type' => 'fb_exchange_token',
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'fb_exchange_token' => $this->whatsappToken,
]);


        if ($response->successful()) {
            return response()->json([
                'status' => 'success',
                'data' => $response->json(),
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $response->json(),
        ], $response->status());
    } catch (\Exception $e) {
        \Log::error("Refresh Token Error: " . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}

}
