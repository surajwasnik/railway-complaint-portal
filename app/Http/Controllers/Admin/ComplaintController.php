<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Exception;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint;
use App\Models\Station;

class ComplaintController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
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

                Complaint::create([
                    'station_id'            => $stationId,
                    'fir_number'            => $data['fir_number'] ?? null,
                    'complainant_name'      => $data['complainant_name'] ?? null,
                    'fir_description'       => $data['fir_description'] ?? null,
                    'user_description'      => $data['user_description'] ?? null,
                    'police_station_name'   => $data['police_station_name'] ?? null,
                    'officer_name'          => $data['officer_name'] ?? null,
                    'police_station_number' => $data['police_station_number'] ?? null,
                    'status'                => $data['status'] ?? 'pending',
                    'fir_date'              => $data['fir_date'] ?? null,
                ]);
            }

            fclose($handle);

            return redirect()
                ->route('voyager.complaints.index')
                ->with(['message' => 'Complaints imported successfully!', 'alert-type' => 'success']);
        } catch (Exception $e) {
            \Log::error('Error importing complaints: ' . $e->getMessage());
            return redirect()
                ->route('voyager.complaints.index')
                ->with(['message' => 'Error importing complaints: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }
}
