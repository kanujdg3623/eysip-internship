<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use \Torann\GeoIP\Facades\GeoIP;
use App\Client;
use App\Visitor;

use Illuminate\Support\Carbon; 

class ClientController extends Controller
{
    public function index(Request $request){

        $value = $request->cookie('_eYClientID');

        $visitorCookieValue = $request->cookie('_eYvisitor');

        if($visitorCookieValue == ""){
            return response()->json(['errors' => "No Visitor ID present"], 500);
        }

        $client = $this->createOrEdit($request, $value);
        
        if($client){
            $response = new Response('ClientID');

            $expires = 60 * 24 * 365 * 2; // 2 yr

            $response->withCookie(cookie('_eYClientID', $client->client_id, $expires));
            
            return $response;
        }else{
            return response()->json(['errors' => "Header X-OS-Browser not passed"], 500);
        }
    }

    public function analysis(Request $request){

        if($request["fromDate"] == "" && $request["toDate"] == "" ){
            $start_date = Carbon::now()->startOfMonth();
            $end_date = Carbon::now();
        } elseif ($request["fromDate"] == "") {
            try{
                $start_date = Carbon::now()->startOfMonth();
                $end_date = Carbon::createFromFormat('Y-m-d', $request["toDate"]);
                if($start_date->greaterThan($end_date)){
                    return response()->json(['errors' => "Start date greater than end date"], 500);
                } 
            }catch (\Throwable $th) {
                error_log($th);
            }
        } elseif ($request["toDate"] == "") {
            try {
                $start_date = Carbon::createFromFormat('Y-m-d', $request["fromDate"]);
                $end_date = Carbon::now();
                if($end_date->lessThan($start_date)){
                    return response()->json(['errors' => "End date lesser than start date"], 500);
                }
            } catch (\Throwable $th) {
                error_log($th);
            }
        }else{
            try {
                $start_date = Carbon::createFromFormat('Y-m-d', $request["fromDate"]);
                $end_date = Carbon::createFromFormat('Y-m-d', $request["toDate"]);
                if($start_date->greaterThan($end_date)){
                    return response()->json(['errors' => "Start date greater than end date"], 500);
                }
            } catch (\Throwable $th) {
                error_log($th);
            }
        }
        $from = date($start_date);
        $to = date($end_date);

        $clients = Client::whereBetween('updated_at', [$from, $to])->get();

        foreach ($clients as $client) {
            $is_returning = array();
            $client["is_new"] = false;
            for ($i=count($client->vis_session_id)-1; $i >= 0 ; $i--) { 
                $visitor = Visitor::where('vis_session_id', $client->vis_session_id[$i])->first();
                $visitor_created_at = new Carbon($visitor->created_at);
                if($visitor_created_at->between($start_date, $end_date)){
                    array_push($is_returning,$visitor_created_at);
                    if ($i==0) {
                        $client->is_new = true;
                    }
                }
            }
            $client["is_returning"] = $is_returning;
            unset($client->vis_session_id);
        }
        return \Response::json(array("result"=>$clients, "fromDate"=> $from, "toDate"=> $to));

    }

    private function createOrEdit($request, $client_id=null) {
        try {
            $client = Client::where('client_id', $client_id)->firstOrFail();
            $vis_session_array = $client->vis_session_id;
            if(end($vis_session_array)!==$request->cookie('_eYvisitor')){
                array_push($vis_session_array,$request->cookie('_eYvisitor'));
                $client->vis_session_id = $vis_session_array;
            }
            $client->save();
        } catch (ModelNotFoundException $e) {
            if ($request->header('X-Os-Browser')) {
                $client = new Client();
                    
                $client->client_id = Str::orderedUuid();
                $client->IP = $request->ip();
                
                $location = geoip()->getLocation($request->ip());
                $client->city = $location["city"];
                $client->state = $location["state_name"];
                $client->country = $location["country"];
                $client->longitude = $location["lon"];
                $client->latitude = $location["lat"];
                $client->os_browser =  $request->header('X-Os-Browser');
                $client->vis_session_id = [$request->cookie('_eYvisitor')];
                $client->save();
            } else {
                $client = null;
            }   
        }
        return $client;
    }
}
