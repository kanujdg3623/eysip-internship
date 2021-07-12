<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Visitor;

use Illuminate\Support\Carbon; 

class VisitorController extends Controller
{
    public function index(Request $request){

        $value = $request->cookie('_eYvisitor');
        $client_value = $request->cookie('_eYClientID') ? $request->cookie('_eYClientID') : null;

        if($request["hostname"] == "" ||  $request["pathname"] == ""){
            return response()->json(['errors' => "No hostname or pathname passed"], 500);
        }

        $vis_session_id = $this->createOrEdit($request, $value, $client_value);
        $response = new Response($vis_session_id." ".$client_value);

        $start_time = Carbon::now(); 
        $final_time = Carbon::tomorrow()->addMinutes(180);
        $expires = $start_time->diffInSeconds($final_time)/60; // 3:00am next day

        $response->withCookie(cookie('_eYvisitor', $vis_session_id, $expires));
        
        return $response;
    }

    public function show(Request $request) {

        $visitors = Visitor::all();

        $visit_data = array("total_visits"=>0, "returning_visits"=>0, "unique_visits"=>0);

        foreach ($visitors as $visitor) {
            // $is_today = 0;
            // $dt = new Carbon($visitor["created_at"]);

            // if($dt->isToday()){
            //     $is_today = 1;
            //     $visit_data["unique_visits_today"] += 1;
            //     $visit_data["returning_visits_today"] += (count($visitor["vis_session_details"])-1)?1:0;
            //     $visit_data["total_visits_today"] += count($visitor["vis_session_details"]);
            // };
            $visit_data["unique_visits"] += 1;
            $visit_data["returning_visits"] += (count($visitor["vis_session_details"])-1)?1:0;
            $visit_data["total_visits"] += count($visitor["vis_session_details"]);
            // foreach ($visitor["vis_session_details"] as $sess) {
            //     if($sess == 0){
            //         $visit_data["bounces"] += 1;
            //         if($is_today){
            //             $visit_data["bounces_today"] += 1;
            //         }
            //     }
            // }
        }; 
                
        return \Response::json($visit_data);

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

        $visitors = Visitor::whereBetween('created_at', [$from, $to])->get();

        return \Response::json(array("result"=>$visitors, "fromDate"=> $from, "toDate"=> $to));

    }

    public function update(Request $request) {

        $value = $request->cookie('_eYvisitor');
        $client_value = $request->cookie('_eYClientID') ? $request->cookie('_eYClientID') : null;

        if($request["hostname"] == "" ||  $request["pathname"] == ""){
            return response()->json(['errors' => "No hostname or pathname passed"], 500);
        }
        if($request["ticks"] == ""){
            return response()->json(['errors' => "No ticks passed"], 500);
        } else{
            try {
                $ticks = number_format($request["ticks"]);
            } catch(\Exception $e) {
                return response()->json(['errors' => "Improper ticks passed"], 500);
            }

            try {
                $visitor = Visitor::where('vis_session_id', $value)->firstOrFail();

                $vis_session_array = $visitor->vis_session_details;
                $vis_session_array_end = end($vis_session_array);

                array_pop($vis_session_array);

                $sess_updated_time = Carbon::now(); 
                $vis_session_array_end["updated_at"] = $sess_updated_time;
                $vis_session_array_end["duration"] = (int)$ticks;

                array_push($vis_session_array,$vis_session_array_end);
                
                $visitor->vis_session_details = $vis_session_array;
                $visitor->client_id = $client_value;
            } catch (ModelNotFoundException $e) {
                $visitor = new Visitor();
                $visitor->vis_session_id = Str::orderedUuid();
                // Timer from zero and page uri info
                $sess_start_time = Carbon::now(); 
                $uri_detail = array("hostname"=>$request["hostname"], "pathname"=> $request["pathname"]);
                $sess_detail = array("URI"=>$uri_detail, "duration"=>(int)$ticks,"created_at"=>$sess_start_time,"updated_at"=>$sess_start_time );

                $visitor->vis_session_details = [$sess_detail];
                $visitor->client_id = $client_value;;
            }

            $visitor->save();

            $response = new Response('VisitorID');

            $start_time = Carbon::now(); 
            $final_time = Carbon::tomorrow()->addMinutes(180);
            $expires = $start_time->diffInSeconds($final_time)/60; // 3:00am next day

            $response->withCookie(cookie('_eYvisitor', $visitor->vis_session_id, $expires));
                
            return $response;
        }

    }

    private function createOrEdit($request, $vis_session_id, $client_value) {
        try {
            $visitor = Visitor::where('vis_session_id', $vis_session_id)->firstOrFail();
            $vis_session_array = $visitor->vis_session_details;
            // Timer from zero and page uri info
            $sess_start_time = Carbon::now(); 
            $uri_detail = array("hostname"=>$request["hostname"], "pathname"=> $request["pathname"]);
            $sess_detail = array("URI"=>$uri_detail, "duration"=>0,"created_at"=>$sess_start_time,"updated_at"=>$sess_start_time );

            array_push($vis_session_array,$sess_detail);
            $visitor->vis_session_details = $vis_session_array;
            $visitor->client_id = $client_value;
        }
        catch(ModelNotFoundException $e) {
            $visitor = new Visitor();
            $visitor->vis_session_id = Str::orderedUuid();
            // Timer from zero and page uri info
            $sess_start_time = Carbon::now(); 
            $uri_detail = array("hostname"=>$request["hostname"], "pathname"=> $request["pathname"]);
            $sess_detail = array("URI"=>$uri_detail, "duration"=>0,"created_at"=>$sess_start_time,"updated_at"=>$sess_start_time );

            $visitor->vis_session_details = [$sess_detail];
            $visitor->client_id = null;
        }
        
        $visitor->save();

        return $visitor->vis_session_id;

    }
    
}
