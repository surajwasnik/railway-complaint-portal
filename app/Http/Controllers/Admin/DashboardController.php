<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\PhotoApprovals;
use Carbon\Carbon;
use Helper;
use App\Models\Complaint;
class DashboardController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    public function dashboard()
    {
        $user = Auth::user();
        if(!$user){
            return Voyager::view('voyager::login');
        }
        // $total_declined  = $total_approved = 0;

        // $total_declined  = Helper::get_photodata_by_status(0);
        // $total_approved  = Helper::get_photodata_by_status(1);

        //  $recent_gen_photo = PhotoApprovals::orderBy('id', 'DESC')->limit(5)->get()->toArray();

        // $data = [
        //     'total_declined'    => $total_declined,
        //     'total_approved'    => $total_approved,
        //     'recent_gen_photo'  => $recent_gen_photo,
        // ];

       // return Voyager::view('voyager::index', compact('data'));
        return Voyager::view('voyager::index');
    }
      public function firDetails(Request $request, $id)
    {
       $complaint = Complaint::where('fir_number', $id)->first();

        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $complaint
        ]);
    }
}
