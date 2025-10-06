<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;
use App\Models\Complaint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Station;
class DashboardController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{

    private $clientId;
    private $clientSecret;
    private $verifyToken;
    private $whatsappAccessToken;
    private $phoneNumberId;

    public function __construct()
    {
        $this->clientId       = setting('admin.whatsapp_client_id');
        $this->clientSecret   = setting('admin.whatsapp_client_secret');
        $this->verifyToken    = setting('admin.whatsapp_verify_token');
        $this->whatsappAccessToken  = setting('admin.whatsapp_access_token');
        $this->phoneNumberId  = setting('admin.whatsapp_phone_number_id');
    }

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
                Log::info($message);
                $from = $message['from'];
                $check_number = "";
    
                if (substr($from, 0, 2) === "91") {
                    $check_number = substr($from, 2);
                }
                $text = trim($message['text']['body'] ?? "");
    
                $cacheKey = "fir_attempts_" . $from;
                $attempts = cache()->get($cacheKey, 0);
                cache()->forget("fir_attempts_919923280712");

                if ($attempts >= 3) {
                    return;
                }
    
                $attempts++;
                $expiry = now()->endOfDay();
                cache()->put($cacheKey, $attempts, $expiry);
    
                $complaint = Complaint::where('fir_number', $text)->first();
    
                if ($complaint) {
                    if ($complaint->complainant_number == $check_number) {
                        $this->sendWhatsAppMessage(
                            $from,
                            $complaint->name ?? "User",
                            $complaint->fir_number,
                            $complaint->fir_date ?? now()->toDateString(),
                            $complaint->station->station_name ?? "Police Station",
                            $complaint->io_officer ?? "Officer Name",
                            $complaint
                        );
                    } 
                    else {
                        
                        // $this->sendRawWhatsAppMessage(
                        //     $from,
                        //     "You are not allowed to view FIR status from this WhatsApp number.\n\n" .
                        //     "Please use the registered WhatsApp number or contact the Police Station."
                        // );
                        $this->sendRawWhatsAppMessage(
                        $from,
                        "You are not allowed to view FIR status from this WhatsApp number.\n\n" .
                        "Please use the registered WhatsApp number or contact the Police Station.\n\n" .
                        "तुम्हाला या व्हाट्सअॅप नंबरवरून एफआयआर स्टेटस पाहण्याची परवानगी नाही.गी नाही.\n\n" .
                        "कृपया नोंदणीकृत व्हाट्सअॅप नंबर वापरा किंवा पोलिस स्टेशनशी संपर्क साधा. संपर्क साधा."
                    );

                    }
                } 
                else {
                    $this->sendRawWhatsAppMessage(
                        $from,
                        "Your FIR number was not found. Please try again with the correct FIR number.\n\n" .
                        "Example: FIR2024-123456\n\n" .
                        "Attempts used: {$attempts}/3\n\n" .
                        "You have used all the attempts for today.\n Please try after 24 hours for update.\n\n" .
                        "तुमचा एफआयआर नंबर सापडला नाही. कृपया योग्य एफआयआर नंबरसह पुन्हा प्रयत्न करा.\n\n".
                        "उदाहरण: FIR2024-123456\n\n".
                        "वापरलेले प्रयत्न:{$attempts}/3\n\n".
                        "तुमचे आजचे पूर्ण प्रार्थना नमुना अहेत कृपाया 24 तस नंतर परात पुनहा प्र प्रयत्न करा."
                    );
                }
            }
    
            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            Log::error("Webhook error: " . $e->getMessage());
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
    $response = Http::withOptions(['verify' => false])->withToken($this->whatsappAccessToken)->post($url, $payload);

        Log::info("Sent bilingual message:", $response->json());
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

        $response = Http::withOptions(['verify' => false])->withToken($this->whatsappAccessToken)->post($url, $payload);

        Log::info("Sent simple WhatsApp message:", $response->json());
        return $response->json();
    }

    public function refreshFacebookToken()
    {
        try {
    
            $url = "https://graph.facebook.com/v23.0/oauth/access_token";
    
            $response = Http::withoutVerifying()->get($url, [
                'grant_type' => 'fb_exchange_token',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'fb_exchange_token' => $this->whatsappAccessToken,
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
            Log::error("Refresh Token Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
 
    public function index(Request $request)
    {
        $user = Auth::user();
        if(!$user){
            return Voyager::view('voyager::login');
        }
        if ($user->role_id == 2) {
            $stationIds = Station::where('user_id', $user->id)->pluck('id');
            $totalComplaints = Complaint::whereIn('station_id', $stationIds)->count();
            $pendingComplaints = Complaint::whereIn('station_id', $stationIds)->where('status', 2)->count();
            $investigationComplaints = Complaint::whereIn('station_id', $stationIds)->where('status', 2)->count();
            $detectedRecoveredComplaints = Complaint::whereIn('station_id', $stationIds)->where('status', 3)->count();
        }else{
            $totalComplaints = Complaint::count();
            $pendingComplaints = Complaint::where('status', 2)->count();
            $investigationComplaints = Complaint::where('status', 2)->count();
            $detectedRecoveredComplaints = Complaint::where('status', 3)->count();
        }
        
        $complaints = Helper::getComplaints(10);
        
        return view('vendor.voyager.dashboard.index', compact(
            'totalComplaints',
            'pendingComplaints',
            'investigationComplaints',
            'detectedRecoveredComplaints',
            'complaints'
        ));

    }

}