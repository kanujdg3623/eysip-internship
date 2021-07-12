<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon; 

use App\Course;
use App\CourseVideo;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $course_search = $request["course_search"]?$request["course_search"]:"";
        if($course_search==""){
            $courses = Course::latest()->take(20)->get();
        }else{
            $courses = Course::whereRaw("`name` LIKE '%". strtoupper($course_search)."%'")->get();
        }
        foreach ($courses as $course) {
            try {
                $course->registered_users_count = count($course->registered_users);
            } catch (\Throwable $th) {
                $course->registered_users_count = 0;
            }
        }
        return view('course.index',[ 'courses'=> $courses, 'course_search'=>$course_search ]);
    }

    public function show($id) {
        
        $course = Course::findOrFail($id);
        $course_videos = CourseVideo::where('course_id',$id)->get();

        return view('course.show',[ 'course'=> $course, 'course_videos'=>$course_videos ]);
    }

    // Admin Controls
    public function create(Request $request)
    {
        return view('course.create',[ 'type'=> "Create", 'course'=>null ]);
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        return view('course.create',[ 'type'=> "Edit", 'course'=>$course ]);
    }

    public function store(Request $request)
    {
        if($request->type=="Create"){
            $course = new Course();
            $message = "Course Created";
            $course->course_videos= [];
        } else{
            $course = Course::findOrFail($request->course_id);
            $message = "Course Edited";
        }

        $course->name = $request->course_name;
        $course->description= $request->course_description;
        $course->skillset= $request->course_skillset;

        $course->save();

        return redirect("admin/course/".$course->id)->with('message', $message);

    }
}
