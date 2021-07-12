<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JD\Cloudder\Facades\Cloudder;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    
    public function edit(){
    	return view('user.edit');
    }
    public function update(){
    	$data=request()->validate([
    		'name'=>'required',
    		'contact_number'=>'required|digits:10',
    		'image'=>'image|between:1,6000',
    	]);
    	if(request('image')){
    		$imagePath=request()->file('image')->getRealPath();
    		Cloudder::upload($imagePath,null);
    		$image=Cloudder::show(Cloudder::getPublicId(),array("width" => 1000, "height" => 1000, "crop"=>"scale"));
    		$imageArray=['image'=>$image];
    		/*$imagePath=request('image')->store('profile','public');    		
    		$image=Image::make(public_path("storage/{$imagePath}"))->fit(1000,1000);
    		$image->save();*/
    	}
    	auth()->user()->update(array_merge(
    		$data,
    		$imageArray??[]
    	));
    	return redirect("/home");
    }
}
