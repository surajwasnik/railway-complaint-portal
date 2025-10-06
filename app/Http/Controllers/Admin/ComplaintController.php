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
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

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


    public function store(Request $request)
    {
       $rules = [
            'language' => 'required|in:english,marathi',
        ];
        if ($request->input('language') === 'marathi') {
            $rules = [
                'station_id_mr'        => 'required|exists:stations,id',
                'fir_number_mr'        => 'required|string|unique:complaints,fir_number',
                'complainant_name_mr'  => 'required|string|max:255',
                'complainant_number_mr'=> 'required|string|max:50',
                'officer_name_mr'      => 'required|string|max:255',
                'police_station_number_mr' => 'nullable|string|max:50',
                'status_mr'            => 'required',
                'fir_date_mr'          => 'required|date',
            ];

    $data = [
            'station_id'            => $request->station_id_mr,
            'fir_number'            => $request->fir_number_mr,
            'complainant_name'      => $request->complainant_name_mr,
            'complainant_number'    => $request->complainant_number_mr,
            'fir_description'       => $request->fir_description_mr,
            'user_description'      => $request->user_description_mr,
            'officer_name'          => $request->officer_name_mr,
            'police_station_number' => $request->police_station_number_mr,
            'status'                => $request->status_mr,
            'fir_date'              => $request->fir_date_mr,
            'language'              => $request->language,
        ];
        } else {
            $rules = [
                'station_id'           => 'required|exists:stations,id',
                'fir_number'           => 'required|string|unique:complaints,fir_number',
                'complainant_name'     => 'required|string|max:255',
                'complainant_number'   => 'required|string|max:50',
                'officer_name'         => 'required|string|max:255',
                'police_station_number'=> 'nullable|string|max:50',
                'status'               => 'required',
                'fir_date'             => 'required|date',
            ];

            $data = $request->only([
                'station_id',
                'fir_number',
                'complainant_name',
                'complainant_number',
                'fir_description',
                'user_description',
                'officer_name',
                'police_station_number',
                'status',
                'fir_date',
                'language'
            ]);
        }
    $data['language'] = $request->input('language');
        $request->validate($rules);

        Complaint::create($data);

        return redirect()
            ->route('voyager.complaints.index')
            ->with(['message' => 'Complaint created successfully!', 'alert-type' => 'success']);
    }


    public function update(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

       $lang = $request->input('language');
    if ($lang === 'marathi') {
        $rules = [
            'station_id_mr'            => 'required|exists:stations,id',
            'fir_number_mr'            => 'required|string|unique:complaints,fir_number,' . $complaint->id,
            'complainant_name_mr'      => 'required|string|max:255',
            'complainant_number_mr'    => 'required|string|max:50',
            'officer_name_mr'          => 'required|string|max:255',
            'police_station_number_mr' => 'nullable|string|max:50',
            'status_mr'                => 'required',
            'fir_date_mr'              => 'required|date',
        ];

        $data = [
            'station_id'            => $request->station_id_mr,
            'fir_number'            => $request->fir_number_mr,
            'complainant_name'      => $request->complainant_name_mr,
            'complainant_number'    => $request->complainant_number_mr,
            'fir_description'       => $request->fir_description_mr,
            'user_description'      => $request->user_description_mr,
            'officer_name'          => $request->officer_name_mr,
            'police_station_number' => $request->police_station_number_mr,
            'status'                => $request->status_mr,
            'fir_date'              => $request->fir_date_mr,
        ];
    } else {
        $rules = [
            'station_id'           => 'required|exists:stations,id',
            'fir_number'           => 'required|string|unique:complaints,fir_number,' . $complaint->id,
            'complainant_name'     => 'required|string|max:255',
            'complainant_number'   => 'required|string|max:50',
            'officer_name'         => 'required|string|max:255',
            'police_station_number'=> 'nullable|string|max:50',
            'status'               => 'required',
            'fir_date'             => 'required|date',
        ];

        $data = $request->only([
            'station_id',
            'fir_number',
            'complainant_name',
            'complainant_number',
            'fir_description',
            'user_description',
            'officer_name',
            'police_station_number',
            'status',
            'fir_date',
        ]);
    }
    $request->validate($rules);

    $complaint->update($data);
        return redirect()
            ->route('voyager.complaints.index')
            ->with(['message' => 'Complaint updated successfully!', 'alert-type' => 'success']);
    }


    public function import(Request $request)
    {
        try {
            $request->validate([
                'csv_file' => 'required|file|mimes:csv'
            ]);

            $file = $request->file('csv_file');
            $handle = fopen($file->getRealPath(), 'r');

            $header = fgetcsv($handle, 1000, ',');
            $user   = Auth::user();

            $station = Station::where('user_id', $user->id)->first();

            if (!$station) {
                return redirect()
                    ->route('voyager.complaints.index')
                    ->with(['message' => 'No station linked to your account!', 'alert-type' => 'error']);
            }

            $stationId = $station->id;

            $statusMap = [
                2 => 'Under Investigation',
                3 => 'Detected & Property Recovered',
                4 => 'Detected but Property Not Recovered',
                5 => 'Mobile Recovered – Collect from PS',
                6 => 'Not Detected – Closure Report Filed',
            ];

            $statusLookup = array_flip($statusMap);

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $data = array_combine($header, $row);

                $statusValue = $statusLookup[$data['status']] ?? 1;

                Complaint::firstOrCreate(
                    [
                        'station_id' => $stationId,
                        'fir_number' => $data['fir_number'] ?? null,
                    ],
                    [
                        'complainant_name'      => $data['name'] ?? null,
                        'complainant_number'    => $data['number'] ?? null,
                        'police_station_number' => $data['io_officer_number'] ?? null,
                        'status'                => $statusValue,
                        'fir_date'              => $data['fir_date'] ?? null,
                        'officer_name'          => $data['io_officer'] ?? null,
                        'language'              => 'english',
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



    public function edit(Request $request, $id)
        {
            $slug = $this->getSlug($request);

            $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

            if (strlen($dataType->model_name) != 0) {
                $model = app($dataType->model_name);
                $query = $model->query();

                if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                    $query = $query->withTrashed();
                }
                if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
                    $query = $query->{$dataType->scope}();
                }
                $dataTypeContent = call_user_func([$query, 'findOrFail'], $id);
            } else {
                $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
            }

            foreach ($dataType->editRows as $key => $row) {
                $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
            }

            $this->removeRelationshipField($dataType, 'edit');
            $this->authorize('edit', $dataTypeContent);
            $isModelTranslatable = is_bread_translatable($dataTypeContent);
            $this->eagerLoadRelations($dataTypeContent, $dataType, 'edit', $isModelTranslatable);
             $data = Complaint::find($id);
            $view = 'voyager::bread.edit-add';

            if (view()->exists("voyager::$slug.edit-add")) {
                $view = "voyager::$slug.edit-add";
            }

            return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable','data'));
        }

        public function getServerData(Request $request)
        {
            $user = auth()->user();

            $query = Complaint::select(
                    'complaints.*',
                    'stations.station_name as police_station_name'
                )
                ->leftJoin('stations', 'complaints.station_id', '=', 'stations.id');

            if ($user->role_id == 2) {
                $stationIds = Station::where('user_id', $user->id)->pluck('id');
                $query->whereIn('complaints.station_id', $stationIds);
            }

            if ($request->station) {
                $query->where('stations.station_name', $request->station);
            }
            if ($request->fir_number) {
                $query->where('complaints.fir_number', 'like', "%{$request->fir_number}%");
            }
            if ($request->status) {
                $query->where('complaints.status', $request->status);
            }
            if ($request->date_from && $request->date_to) {
                $query->whereBetween('complaints.fir_date', [$request->date_from, $request->date_to]);
            } elseif ($request->date_from) {
                $query->whereDate('complaints.fir_date', '>=', $request->date_from);
            } elseif ($request->date_to) {
                $query->whereDate('complaints.fir_date', '<=', $request->date_to);
            }

            $statusMap = [
                2 => 'Under Investigation',
                3 => 'Detected & Property Recovered',
                4 => 'Detected but Property Not Recovered',
                5 => 'Mobile Recovered – Collect from PS',
                6 => 'Not Detected – Closure Report Filed',
            ];

            return DataTables::of($query)
                ->editColumn('police_station_name', function ($row) {
                    return $row->police_station_name;
                })
                ->editColumn('status', function ($row) use ($statusMap) {
                    return $statusMap[$row->status] ?? $row->status;
                })
                ->filterColumn('police_station_name', function($query, $keyword) {
                    $query->where('stations.station_name', 'like', "%{$keyword}%");
                })
                ->orderColumn('police_station_name', function ($query, $order) {
                    $query->orderBy('stations.station_name', $order);
                })
                ->editColumn('fir_date', function ($row) {
                    return $row->fir_date
                        ? \Carbon\Carbon::parse($row->fir_date)->format('d/m/Y')
                        : '';
                })
                ->addColumn('actions', function ($row) {
                    $view = '';
                    $delete = '';

                    if (auth()->user()->can('edit', $row)) {
                        $view = '<a href="' . route('voyager.complaints.edit', $row->id) . '"
                                    class="btn btn-sm btn-primary edit">
                                    <i class="fa-regular fa-eye"></i> View
                                 </a>';
                    }

                    if (auth()->user()->can('delete', $row)) {
                        $delete = ' <button data-id="' . $row->id . '"
                                        class="btn btn-sm btn-danger delete">
                                        <i class="fa-regular fa-trash-can"></i> Delete
                                    </button>';
                    }

                    return $view . ' '.$delete;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }


}
