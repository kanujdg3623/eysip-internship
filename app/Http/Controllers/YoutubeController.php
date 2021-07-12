<?php

namespace App\Http\Controllers;
use Google_Client;
use Google_Service_YouTube;
use Illuminate\Http\Request;
use App\Videos;
use DateTime;
class YoutubeController extends Controller
{
    //
    public function subscribeYoutube(Request $request){
    	return $request->all()['hub_challenge'];
    }  
    public function youtubeNotification(Request $request){
    	$xml=$request->getContent();
    	$channelId=substr($xml,strpos($xml,'<yt:channelId>')+14,strpos($xml,'</yt:channelId>')-strpos($xml,'<yt:channelId>')-14);
    	if($channelId!='UCWfSeyt5dV39luJknVQhFzA') return;
    	$videoId= substr($xml,strpos($xml,'<yt:videoId>')+12,strpos($xml,'</yt:videoId>')-strpos($xml,'<yt:videoId>')-12);
    	$client=$this->getClient();
		if(!$client) return;
		$service = new Google_Service_YouTube($client);
		$queryParams = [
			'id' => $videoId
		];
		$response = $service->videos->listVideos('snippet', $queryParams);
		$video = $response->items[0];
		if(!Videos::where('videoId',$video->id)->update([
				'published_at' => new DateTime($video->snippet->publishedAt),
				'title' => $video['snippet']['title'],
				'description' => $video['snippet']['description'],
				'initiative' => $this->getInitiative($video['snippet']['title']),
				'year' => $this->getYear($video['snippet']['title'])
			])){
			Videos::create([
				'videoId' => $video->id,
				'published_at' => new DateTime($video->snippet->publishedAt),
				'initiative' => $this->getInitiative($video['snippet']['title']),
				'year' => $this->getYear($video['snippet']['title']),
				'title' => $video['snippet']['title'],
				'description' => $video['snippet']['description']
			]);
		}    		
    }
	
	public function getInitiative($title){
		preg_match("/(Talk|eYRC|eYIC|eLSI|eYSIP|eYS|eFSI|eYRTC|MOOC|Hackathon|eYRDC|e-Yantra Impact|ERTS|.*Workshop|TBT)/i",$title,$match);
		return $match[0] ?? 'eYantra';
	}
	
	public function getYear($title){
		preg_match("/20\d\d/",$title,$match);
		return $match[0] ?? 0;
	}
	
    public function getClient(){
		$client = new Google_Client();
		$client->setApplicationName('eysip videos');
		$client->setScopes('https://www.googleapis.com/auth/youtube.readonly');
		$client->setAuthConfig(public_path('client_secret.json'));
		$client->setAccessType('offline');
		$client->setPrompt('select_account consent');
		// Load previously authorized token from a file, if it exists.
		// The file token.json stores the user's access and refresh tokens, and is
		// created automatically when the authorization flow completes for the first
		// time.
		$tokenPath = public_path('token.json');
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
	public function updateVideos(){
		$client=$this->getClient();
    	$service = new Google_Service_YouTube($client);
    	$queryParams = [
			'maxResults' => 50,
			'playlistId' => 'UUWfSeyt5dV39luJknVQhFzA'
		];

		$response = $service->playlistItems->listPlaylistItems('snippet', $queryParams);
		while($response->nextPageToken){
			for($i=0,$len=count($response);$i<$len;$i++){
				$video = $response[$i];
				if(!Videos::where('videoId',$video->snippet->resourceId->videoId)->update([
						'published_at' => new DateTime($video->snippet->publishedAt),
						'title' => $video['snippet']['title'],
						'description' => $video['snippet']['description'],
						'initiative' => $this->getInitiative($video['snippet']['title']),
						'year' => $this->getYear($video['snippet']['title'])
					])){
					Videos::create([
						'videoId' => $video->snippet->resourceId->videoId,
						'published_at' => new DateTime($video->snippet->publishedAt),
						'initiative' => $this->getInitiative($video['snippet']['title']),
						'year' => $this->getYear($video['snippet']['title']),
						'title' => $video['snippet']['title'],
						'description' => $video['snippet']['description']
					]);
				}    
			}
			$queryParams['pageToken']=$response->nextPageToken;
			$response = $service->playlistItems->listPlaylistItems('snippet', $queryParams);
		}
		dd('success');
	}
}
