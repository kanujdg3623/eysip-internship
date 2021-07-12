<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\User;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
    public function check(Request $request)
    {
    	$user=User::where('email',$request->email);
        if($user->exists()){ 
        	if( $user->first()->approved_by=="pending")       	
	        	return redirect(route('password.request'))->with('status', 'Wait for administrator\'s approval');
        }
    	return $this->sendResetLinkEmail($request);
    }
}
