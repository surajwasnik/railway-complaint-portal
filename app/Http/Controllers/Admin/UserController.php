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
        $users = User::with('role')->paginate(10);
         $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        return Voyager::view($view, compact(
            'users','dataType'
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
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'station_name' => 'required|string|max:255',
            'station_code' => 'required|string|max:50|unique:stations,station_code',
            'station_head_name' => 'nullable|string|max:255',
            'station_head_phone' => 'nullable|string|max:15',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'status' => 'in:active,inactive,suspended',
        ]);

        // Save User
        $user = User::create([
            'role_id' => $request->role_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Save Station linked with user
        Station::create([
            'user_id' => $user->id,
            'station_name' => $request->station_name,
            'station_code' => $request->station_code,
            'station_head_name' => $request->station_head_name,
            'station_head_phone' => $request->station_head_phone,
            'city' => $request->city,
            'state' => $request->state,
            'address' => $request->address,
            'status' => $request->status ?? 'active',
        ]);

        return redirect()->back()->with('success', 'User and Station registered successfully!');
    }
    public function edit(Request $request, $id)
    {
        $slug = 'users';
        $user = User::with('station')->findOrFail($id);
        $roles = Role::all();
        $view = 'voyager::bread.edit-add';
        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }
        return Voyager::view($view, compact('user', 'roles'));
    }

    // 🔹 Update logic
    public function update(Request $request, $id)
    {
        $user = User::with('station')->findOrFail($id);

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'station_name' => 'required|string|max:255',
            'station_code' => 'required|string|max:50|unique:stations,station_code,' . $user->station->id,
            'status' => 'in:active,inactive,suspended',
        ]);

        // Update User
        $user->update([
            'role_id' => $request->role_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        // Update Station
        $user->station->update([
            'station_name' => $request->station_name,
            'station_code' => $request->station_code,
            'station_head_name' => $request->station_head_name,
            'station_head_phone' => $request->station_head_phone,
            'city' => $request->city,
            'state' => $request->state,
            'address' => $request->address,
            'status' => $request->status ?? 'active',
        ]);

        return redirect()->back()->with('success', 'User and Station updated successfully!');
    }
}
