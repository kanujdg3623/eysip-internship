<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon; 

use DateTime;
use Aws\CloudFront\CloudFrontClient;

use App\CourseVideo;
use App\Course;

class CourseVideoController extends Controller
{

    public function show($id,$video_id) {   
        $course_video = CourseVideo::findOrFail($video_id);

        return view('course-video.show',[ 'course_video'=> $course_video ]);
    }

    public function get_access_token($id,$video_id) { 
        
        // Tokenising Videos
        // $course_video = CourseVideo::findOrFail($video_id);

        // $s3 = \Storage::disk('s3');
        // $client = $s3->getDriver()->getAdapter()->getClient();
        // $expiry = "+10 minutes";

        // $command = $client->getCommand('GetObject', [
        //     'Bucket' => \Config::get('filesystems.disks.s3.bucket'),
        //     'Key'    => "uploads/".$course_video->video
        // ]);

        // $request = $client->createPresignedRequest($command, $expiry);
        // return (string) $request->getUri();

        // Using Cloudfront
        $cloudfront = new CloudFrontClient([
            'version'     => 'latest',
            'region'      => \Config::get('filesystems.disks.cloudfront.region'),
        ]);
        $course_video = CourseVideo::findOrFail($video_id);
        $expiry = new DateTime("+10 minutes");
        $url = $cloudfront->getSignedUrl([
            'private_key' => \Config::get('filesystems.disks.cloudfront.private_key'),
            'key_pair_id' => \Config::get('filesystems.disks.cloudfront.key_pair_id'),
            'url' => \Config::get('filesystems.disks.cloudfront.url')."/".$course_video->video,
            'expires' => $expiry->getTimestamp(),
        ]);
        return (string) $url;
    }

    // Admin functions
    public function create($id, Request $request)
    {
        return view('course-video.create',[ 'type'=> "Add", 'course_video'=>null, 'course_id'=>$id ]);
    }

    public function edit($id,$video_id)
    {
        $course_video = CourseVideo::findOrFail($video_id);
        return view('course-video.create',[ 'type'=> "Edit", 'course_video'=>$course_video, 'course_id'=>$id ]);
    }

    public function store($id, Request $request)
    {
        if($request->type=="Add"){
            $course_video = new CourseVideo();
            $message = "Video Added";

            // First Upload
            // File verify
            // Change file upload limit in php.ini and post max size to 0
            $file = $request->file('video_file');
            if (substr( $file->getMimeType() , 0, 6 ) !== "video/") {
                redirect()->back()->with('message', "File format must be Video Only.");
            }
            
            $name = md5(uniqid()).".".$file->getClientOriginalExtension();

            $filePath = 'uploads/' . $name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $course_video->video = basename($filePath);
            $course_video->video_url = \Storage::disk('s3')->url($filePath);

            // Update Course Array
            $course = Course::findOrFail($id);
            $course_video_array = $course->course_videos;
            array_push($course_video_array,$course_video->id);
            $course->course_videos = $course_video_array;
            $course->save();

        } else{
            $course_video = CourseVideo::findOrFail($request->course_video_id);
            $message = "Video Edited";
            if($course_video->video_url !== $request->course_video_video_url){
                $course_video->video_url = $request->course_video_video_url;
            } else{
                // First Upload
                // File verify
                // Change file upload limit in php.ini and post max size to 0
                $file = $request->file('video_file');
                if (substr( $file->getMimeType() , 0, 6 ) !== "video/") {
                    redirect()->back()->with('message', "File format must be Video Only.");
                }
                $name = $file->getClientOriginalName();

                $filePath = 'uploads/' . $name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));
                $course_video->video = basename($filePath);
                $course_video->video_url = \Storage::disk('s3')->url($filePath);
            }
        }

        $course_video->name = $request->course_video_name;
        $course_video->course_id = $id;
        $course_video->description= $request->course_video_description;

        $course_video->save();

        return redirect("admin/course/".$course_video->course_id."/".$course_video->id)->with('message', $message);

    }
}
