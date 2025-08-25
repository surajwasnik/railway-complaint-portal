<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint;
use App\Models\Station;
use Illuminate\Support\Facades\Log;
use TCG\Voyager\Facades\Voyager;

class ComplaintController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{

    public function index(Request $request)
    {
        $response = parent::index($request);
        $response->with([
            'dataTypeContent' => Helper::getComplaints()
        ]);
        return $response;
    }

//     public function create(Request $request)
// {
//     $slug = $this->getSlug($request);

//     $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
//     $this->authorize('add', app($dataType->model_name));

//     $dataTypeContent = strlen($dataType->model_name) != 0
//         ? new $dataType->model_name()
//         : false;

//     // Prefill if station admin
//     $user = auth()->user();
//     // dd($user->role_id);
//     if ($user->role_id == 2) {
//         $station = \App\Models\Station::where('user_id', $user->id)->first();
//         if ($station) {
//             $dataTypeContent->station_id            = $station->id;
//             $dataTypeContent->police_station_name   = $station->station_name;
//             $dataTypeContent->police_station_number = $station->station_head_phone;
//             $dataTypeContent->officer_name          = $station->station_head_name;
//         }
//     }

//     $isModelTranslatable = is_bread_translatable($dataTypeContent);

//     return Voyager::view('voyager::bread.edit-add', compact(
//         'dataType',
//         'dataTypeContent',
//         'isModelTranslatable'
//     ));
// }



    public function import(Request $request)
    {
        try {
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt'
            ]);

            $file = $request->file('csv_file');
            $handle = fopen($file->getRealPath(), 'r');

            $header = fgetcsv($handle, 1000, ',');
            $user   = Auth::user();

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $data = array_combine($header, $row);

                $stationId = null;

                if ($user->role_id === 1) {
                    $stationId = $data['station_id'] ?? null;
                } else {
                    $station = Station::where('user_id', $user->id)->first();

                    if ($station && !empty($data['station_id']) && $data['station_id'] == $station->id) {
                        $stationId = $station->id;
                    }
                }

                if (!$stationId || !Station::find($stationId)) {
                    continue;
                }

                Complaint::firstOrCreate(
                [
                    'station_id' => $stationId,
                    'fir_number' => $data['fir_number'] ?? null,
                ],
                [
                    'complainant_name'      => $data['complainant_name'] ?? null,
                    'fir_description'       => $data['fir_description'] ?? null,
                    'user_description'      => $data['user_description'] ?? null,
                    'police_station_name'   => $data['police_station_name'] ?? null,
                    'officer_name'          => $data['officer_name'] ?? null,
                    'police_station_number' => $data['police_station_number'] ?? null,
                    'status'                => $data['status'] ?? 'pending',
                    'fir_date'              => $data['fir_date'] ?? null,
                ]
            );
            }

            fclose($handle);

            return redirect()
                ->route('voyager.complaints.index')
                ->with(['message' => 'Complaints imported successfully!', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::error('Error importing complaints: ' . $e->getMessage());
            return redirect()
                ->route('voyager.complaints.index')
                ->with(['message' => 'Error importing complaints: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }
}
