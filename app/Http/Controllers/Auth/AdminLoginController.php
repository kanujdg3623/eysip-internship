<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class AdminLoginController extends Controller
{
    //
    public function __construct(){
    	$this->middleware('guest:admin')->except('logout');
    }
    public function showLoginForm(){
    	error_log('Display this on the screen');
    	return view('auth.admin-login');
    }
    
    public function login(Request $request){
    	$this->validate($request,[
    		'email' => 'required|email',
    		'password' => 'required|min:8',
    	]);
    	
    	if(Auth::guard('admin')->attempt([
    		'email' => $request->email,
    		'password' => $request->password,
    	], $request->remember)){
    		return redirect()->intended(route('admin.dashboard'));
    	}
    	return redirect()->back()->withInput($request->only('email','remember'));
    }
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        return $request->wantsJson()
            ? new Response('', 204)
            : redirect('/');
    }
}
