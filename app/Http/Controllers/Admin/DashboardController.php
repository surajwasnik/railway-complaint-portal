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
    private $whatsappToken = "EAAF6WZAWiLfQBPCLmUYq8e7nKCkbak9wXcIDiF9mS5u6eNAbwZBmgYE7NlVyFHpl3lt1YZAPp3s8CVqZAfKM17AZCHK0KBazi86Xui7Sdxs7lyDjH3JNsXdgWDHCDRiQ8SSfhG2tUtY4Vyw93VJIGJKQa7TZCrY6FBPT14tSEvvMzeZBPBZCkCbTC3lz5yFvGM07bCh1w27UihOp9afZCLH4sygq6ZBh9HwxHjgW9ZCJpeD3xeAYtRxJ16qNxwPTqFkNAZDZD";
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
                $text = $message['text']['body'] ?? "";
                $complaint = Complaint::where('fir_number', $text)->first();
                if ($complaint) {
                    $this->sendWhatsAppMessage($from, "User", $complaint->fir_number, now()->toDateString(), "Police Station", "Officer Name", $complaint);
                } else {
                    $this->sendWhatsAppMessage($from, "User", $text, now()->toDateString(), "Police Station", "Officer Name", '');
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
                //"body" => $reply
            ],
        ];
        $response = \Http::withToken($this->whatsappToken)
            ->post($url, $payload);

        \Log::info("Sent bilingual message:", $response->json());
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
    public function firDetails(Request $request, $id)
    {
        $complaint = Complaint::where('fir_number', $id)->first();

        if ($complaint) {
            $status =$complaint->status ?? '';
            $complainant_name =$complaint->complainant_name ?? '';
            $fir_number =$complaint->fir_number ?? '';
            $fir_date =$complaint->fir_date ?? '';
            $police_station_name =$complaint->police_station_name ?? '';
            $officer_name =$complaint->officer_name ?? '';
            $police_station_number =$complaint->police_station_number ?? '';
            $whatsappMessages = Helper::getMessage($status, $complainant_name, $fir_number, $fir_date, $police_station_name, $officer_name, $police_station_number);
            dd($whatsappMessages);
        } else {
            dd('Complaint not found for FIR number: ' . $id);
        }
    }

}
