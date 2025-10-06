<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Station;
use TCG\Voyager\Models\Role;
use Illuminate\Support\Facades\Hash;
class UserController extends VoyagerBaseController
{
     public function index(Request $request)
    {
        $slug = 'users';

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];

        $searchNames = [];
        if ($dataType->server_side) {
            $searchNames = $dataType->browseRows->mapWithKeys(function ($row) {
                return [$row['field'] => $row->getTranslatedAttribute('display_name')];
            });
        }

        $orderBy = $request->get('order_by', $dataType->order_column);
        $sortOrder = $request->get('sort_order', $dataType->order_direction);
        $usesSoftDeletes = false;
        $showSoftDeleted = false;

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            $query = $model::select($dataType->name . '.*');

            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
                $query->{$dataType->scope}();
            }

            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model)) && Auth::user()->can('delete', app($dataType->model_name))) {
                $usesSoftDeletes = true;

                if ($request->get('showSoftDeleted')) {
                    $showSoftDeleted = true;
                    $query = $query->withTrashed();
                }
            }

            $this->removeRelationshipField($dataType, 'browse');

            $query->where('role_id', 2);

            if ($search->value != '' && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%' . $search->value . '%';

                $searchField = $dataType->name . '.' . $search->key;
                if ($row = $this->findSearchableRelationshipRow($dataType->rows->where('type', 'relationship'), $search->key)) {
                    $query->whereIn(
                        $searchField,
                        $row->details->model::where($row->details->label, $search_filter, $search_value)->pluck('id')->toArray()
                    );
                } else {
                    if ($dataType->browseRows->pluck('field')->contains($search->key)) {
                        $query->where($searchField, $search_filter, $search_value);
                    }
                }
            }

            $row = $dataType->rows->where('field', $orderBy)->firstWhere('type', 'relationship');
            if ($orderBy && (in_array($orderBy, $dataType->fields()) || !empty($row))) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'desc';
                if (!empty($row)) {
                    $query->select([
                        $dataType->name . '.*',
                        'joined.' . $row->details->label . ' as ' . $orderBy,
                    ])->leftJoin(
                            $row->details->table . ' as joined',
                            $dataType->name . '.' . $row->details->column,
                            'joined.' . $row->details->key
                        );
                }

                $dataTypeContent = call_user_func([
                    $query->orderBy($orderBy, $querySortOrder),
                    $getter,
                ]);
            } elseif ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest($model::CREATED_AT), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
            }

            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }

        $isModelTranslatable = is_bread_translatable($model);

        $this->eagerLoadRelations($dataTypeContent, $dataType, 'browse', $isModelTranslatable);

        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        $defaultSearchKey = $dataType->default_search_key ?? null;

        $actions = [];
        if (!empty($dataTypeContent->first())) {
            foreach (Voyager::actions() as $action) {
                $action = new $action($dataType, $dataTypeContent->first());

                if ($action->shouldActionDisplayOnDataType()) {
                    $actions[] = $action;
                }
            }
        }

        $showCheckboxColumn = false;
        if (Auth::user()->can('delete', app($dataType->model_name))) {
            $showCheckboxColumn = true;
        } else {
            foreach ($actions as $action) {
                if (method_exists($action, 'massAction')) {
                    $showCheckboxColumn = true;
                }
            }
        }

        $orderColumn = [];
        if ($orderBy) {
            $index = $dataType->browseRows->where('field', $orderBy)->keys()->first() + ($showCheckboxColumn ? 1 : 0);
            $orderColumn = [[$index, $sortOrder ?? 'desc']];
        }

        $sortableColumns = $this->getSortableColumns($dataType->browseRows);

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        return Voyager::view($view, compact(
            'actions',
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'orderColumn',
            'sortableColumns',
            'sortOrder',
            'searchNames',
            'isServerSide',
            'defaultSearchKey',
            'usesSoftDeletes',
            'showSoftDeleted',
            'showCheckboxColumn'
        ));
    }
    public function create(Request $request)
    {
        $slug = 'users';
        $roles = Role::all();
        $user = null;
        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }
        return Voyager::view($view, compact('roles','user'));
    }
    
    public function store(Request $request)
    {
        $slug = 'users';
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
    
        if ($request->language === 'english') {
            $request->merge([
                'station_name' => $request->station_name_en,
                'station_head_name' => $request->station_head_name_en,
                'station_head_phone' => $request->station_head_phone_en,
            ]);
        } elseif ($request->language === 'marathi') {
            $request->merge([
                'station_name' => $request->station_name_mr,
                'station_head_name' => $request->station_head_name_mr,
                'station_head_phone' => $request->station_head_phone_mr,
            ]);
        }
    
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'station_name' => 'required|string|max:255',
            'station_head_name' => 'required|string|max:255',
            'station_head_phone' => 'required|numeric|digits_between:10,15',
            'status' => 'in:active,inactive,suspended',
            'language' => 'nullable'
        ]);
    
        $station = Station::create([
            'station_name' => $request->station_name,
            'station_head_name' => $request->station_head_name,
            'station_head_phone' => $request->station_head_phone,
            'status' => $request->status ?? 'active',
        ]);
    
        $user = User::create([
            'role_id' => $request->role_id,
            'name' => $request->station_head_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'language' => $request->language,
        ]);
    
        $station->update(['user_id' => $user->id]);
    
        return redirect()->route("voyager.{$dataType->slug}.index")
            ->with(['message' => "User and Station registered successfully!", 'alert-type' => 'success']);
    }

    public function edit(Request $request, $id)
    {
        $slug = 'users';
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
        $user = User::with('station')->findOrFail($id);
        $roles = Role::all();
        $view = 'voyager::bread.edit-add';
        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }
        return Voyager::view($view, compact('user', 'roles','dataTypeContent'));
    }

    public function update(Request $request, $id)
{
    $user = User::with('station')->findOrFail($id);

    $request->validate([
        'role_id' => 'required|exists:roles,id',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|min:6|confirmed',
        'status' => 'in:active,inactive,suspended',
        'language' => 'nullable|in:english,marathi',
        'station_head_phone_en' => 'nullable|digits_between:10,15',
        'station_head_phone_mr' => 'nullable|digits_between:10,15',
    ]);

    if ($request->language === 'marathi') {
        $stationName = $request->station_name_mr;
        $stationHeadName = $request->station_head_name_mr;
        $stationHeadPhone = $request->station_head_phone_mr;
    } else {
        $stationName = $request->station_name_en;
        $stationHeadName = $request->station_head_name_en;
        $stationHeadPhone = $request->station_head_phone_en;
    }

    // Update User
    $user->update([
        'role_id' => $request->role_id,
        'name' => $stationHeadName,
        'email' => $request->email,
        'password' => $request->password ? Hash::make($request->password) : $user->password,
        'language' => $request->language,
    ]);

    // Update Station
    $user->station->update([
        'station_name' => $stationName,
        'station_head_name' => $stationHeadName,
        'station_head_phone' => $stationHeadPhone,
        'status' => $request->status ?? 'active',
    ]);
    
    return redirect()->route("voyager.users.index")->with([
        'message' => "Police station user updated successfully!",
        'alert-type' => 'success',
    ]);
}


}
