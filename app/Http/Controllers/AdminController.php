<?php

namespace App\Http\Controllers;
use App\User;
use App\Videos;
use Illuminate\Http\Request;
use App\Mail\NewUserWelcomeMail;
use Illuminate\Support\Facades\Mail;
use Google_Client;
use Google_Service_YouTubeAnalytics;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Support\Facades\DB;
use DateTime;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin',['new'=>User::where('approved_by','pending')->paginate(25)]);
    }

    public function edit(){
    	return view('admin.edit');
    }
    
    public function analytics(Request $request){
    	return view('admin.analytics',['id' => $request->id,'title' => $request->title]);
    }

    public function getAnalytics(Request $request){
    	$client=$this->getClient();
		$analytics = new Google_Service_YouTubeAnalytics($client);

		$optParams=$request->all();

		$api = $analytics->reports->query($optParams);
        return $api->rows;
    }
	
    public function getClient(){
		$client = new Google_Client();
		$client->setApplicationName('eysip videos');
		$client->setScopes('https://www.googleapis.com/auth/yt-analytics.readonly');
		$client->setAuthConfig(public_path('client_secret.json'));
		$client->setAccessType('offline');
		$client->setPrompt('select_account consent');
		// Load previously authorized token from a file, if it exists.
		// The file token.json stores the user's access and refresh tokens, and is
		// created automatically when the authorization flow completes for the first
		// time.
		$tokenPath = public_path('token1.json');
		if (file_exists($tokenPath)) {
		    $accessToken = json_decode(file_get_contents($tokenPath), true);
		    $client->setAccessToken($accessToken);
		}
		// If there is no previous token or it's expired.
		if ($client->isAccessTokenExpired()) {
		    // Refresh the token if possible, else fetch a new one.
		    if ($client->getRefreshToken()) {
		        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
		    } else {
		        error_log("create token with youtube.php");
		        return null;
		    }
		    // Save the token to a file.
		    if (!file_exists(dirname($tokenPath))) {
		        mkdir(dirname($tokenPath), 0700, true);
		    }
		    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
		}
		return $client;
	}

    public function update(){
    	$data=request()->validate([
    		'name'=>'required',
    		'contact_number'=>'required|digits:10',
    		'post' => 'required',
    		'image'=>'image',
    	]);
    	if(request('image')){
    		$imagePath=request()->file('image')->getRealPath();
    		Cloudder::upload($imagePath,null);
    		$image=Cloudder::show(Cloudder::getPublicId(),array("width" => 1000, "height" => 1000, "crop"=>"scale"));
    		$imageArray=['image'=>$image];
    	}
    	auth()->user()->update(array_merge(
    		$data,
    		$imageArray??[]
    	));
    	return redirect(route('admin.dashboard'));
    }
    public function approve($user, Request $request){
    	if($request->prompt=="Decline"){
    		User::where('id',$user)->delete();
    	}
    	else{
    		$USER=User::find($user);
    		Mail::to($USER->email)->send(new NewUserWelcomeMail());
    		$USER->update(['approved_by'=>auth()->user()->name]);
    	}
    	return redirect()->route('admin.dashboard');
    }
}
