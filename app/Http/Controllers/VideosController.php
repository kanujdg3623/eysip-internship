<?php

namespace App\Http\Controllers;
use App\Videos;
use Illuminate\Http\Request;
use Google_Client;
use Google_Service_YouTube;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Article as ArticleResource;
use DateTime;
class VideosController extends Controller
{
    //
	public function index($initiative){
		return view('videos',['initiative'=>$initiative]);
	}
	
	public function analytics(){
		return view('videos');
	}
	
	public function getYear(Request $request){
		$year=DB::table('initiatives_years')->where([['initiative','=',$request->initiative]])->orderByRaw('year DESC')->get(['year','theme']);
		return $year;
	}
	
	public function getVideos(Request $request){
			$initative = DB::table('videos')->where([['initiative', 'like', $request->initiative],['year','like',$request->year]])->orderByRaw('published_at DESC')->get();
		return $initative;
	}
	
	public function getVideo($id){
		return DB::table('videos')->where('videoId',$id)->get();
	}

	public function getYoutubeView(){
		return view('admin.videos');
	}
	public function getYoutubeVideos(Request $request){
		return DB::table('videos')
			->where([['initiative', 'like', $request->initiative],['year','like',$request->year]])
			->orderByRaw('published_at DESC')
			->select('videoId','title','initiative','description','year')
			->paginate(24);
	}
	
	public function updateYoutubeVideo(Request $request){
		if(!Videos::where('videoId',$request['params']['videoId'])
			->update([
				'initiative' => $request['params']['initiative'],
				'year' => $request['params']['year'],
				'title' => $request['params']['title'],
				'description' => $request['params']['description']
			])){
			Videos::create([
				'videoId' => $request['params']['videoId'],
				'published_at' => new DateTime($request['params']['published_at']),
				'initiative' => $request['params']['initiative'],
				'year' => $request['params']['year'],
				'title' => $request['params']['title'],
				'description' => $request['params']['description']
			]);
		}
	}
	
	public function deleteYoutubeVideo(Request $request){
		DB::table('videos')->where(['videoId'=>$request->videoId])->delete();
	}
	
	public function crudInitiatives(Request $request,$crud){
		if($crud=='read')
			return view('admin.initiatives',['table'=>DB::table('initiatives_years')->orderBy('initiative', 'asc')->orderBy('year', 'asc')->get()]);
		else if($crud=='put') {
			if(!DB::table('initiatives_years')
              ->where('id', $request->id)
              ->update($request->all())){
             	DB::table('initiatives_years')->insert($request->except('id')); 
        	}
		}
		else if($crud=='delete'){
			DB::table('initiatives_years')->where('id', '=', $request->id)->delete();
		}		
	}
}
