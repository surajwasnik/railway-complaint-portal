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
class HomeController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{

 public function index(Request $request)
    {
        $user = Auth::user();
        if(!$user){
            return Voyager::view('voyager::login');
        }
         $totalComplaints = Complaint::count();
        $pendingComplaints = Complaint::where('status', 1)->count();
        $investigationComplaints = Complaint::where('status', 2)->count();
        $detectedRecoveredComplaints = Complaint::where('status', 3)->count();

        // All complaints
       $complaints = Complaint::latest()->limit(10)->get();
        return view('vendor.voyager.dashboard.index', compact(
            'totalComplaints',
            'pendingComplaints',
            'investigationComplaints',
            'detectedRecoveredComplaints',
            'complaints'
        ));

    }

}
