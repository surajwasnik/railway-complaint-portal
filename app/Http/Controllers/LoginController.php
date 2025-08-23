<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Session;
use Helper;

class LoginController extends Controller
{
    /**
     * Handle account login request
     *
     * @param LoginRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->getCredentials();
        $userEmail = User::select('id', 'email')->where('role_id', 1)->where('email', $credentials['email'])->first();
        if (!empty($userEmail)) {
            if (Auth::attempt($credentials)) {
                return $this->authenticated($request, $userEmail);
            } else {
                return redirect()->back()->with('error', 'Invalid username or password, please try again later.');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid username or password, please try again later.');
        }
    }

    /**
     * Handle response after user authenticated
     *
     * @param Request $request
     * @param Auth $user
     *
     * @return \Illuminate\Http\Response
     */
    protected function authenticated(Request $request, $user)
{
    return redirect()->route('voyager.dashboard');
}

}
