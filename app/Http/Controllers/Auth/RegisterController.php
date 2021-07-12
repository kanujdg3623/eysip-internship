<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use JD\Cloudder\Facades\Cloudder;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
     
    public function register(Request $request)
	{
		$this->validator($request->all())->validate();
		
		$url='https://www.google.com/recaptcha/api/siteverify';
		$fields=[
			'secret' => env('RECAPTCHA_SECRET_KEY'),
			'response' => $request->all()['g-recaptcha-response']
		];
		$fields_string = http_build_query($fields);
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		if(!(json_decode($result)->success))
			return redirect('/register')->with('status','!!! Captcha error !!!');

		event(new Registered($user = $this->create($request->all())));

		return $this->registered($request, $user)
		                ?: redirect('/login')->with('status',
		                'Your profile is registered. Wait for administrator\'approval'
		                );
	}
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required','numeric','digits:10','unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'gender' => ['required',Rule::in(['male', 'female', 'other'])],
            'image' => ['image'],
            'g-recaptcha-response' => ['required']
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        if(array_key_exists("image",$data)){
        	$imagePath=request()->file('image')->getRealPath();
    		Cloudder::upload($imagePath,null);
    		$image=Cloudder::show(Cloudder::getPublicId(),array("width" => 1000, "height" => 1000, "crop"=>"scale"));
    		$imageArray=['image'=>$image];
        }
        return User::create(array_merge([
            'name' => $data['name'],
            'contact_number' => $data['contact_number'],
            'email' => $data['email'],
            'gender' => $data['gender'],
            'approved_by' => 'pending',
            'password' => Hash::make('eysip')],
        	$imageArray??[]
        ));
        
    }
}
