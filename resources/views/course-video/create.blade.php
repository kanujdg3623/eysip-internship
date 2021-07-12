@extends('layouts.app')

@section('content')
<div class="create-course-videos">
    @if(session('message'))
    <div class="notification is-success is-light" id="CourseMessage">
        {{session('message')}}
    </div>
    @endif
    <section class="hero is-small is-light is-bold">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                {{$type}} Video
                </h1>
            </div>
        </div>
    </section>
    <div class="container">
        <form action="{{ route('admin-course-video.store', $course_id) }}" method="POST" enctype='multipart/form-data'>
            @csrf
            <input hidden value="{{$type}}" name="type" type="text" placeholder="Form Type">
            <input hidden value="{{$course_video?$course_video->id:''}}" name="course_video_id" type="text" placeholder="Course Video Id">
            <div class="field" style="margin-top:10px;padding:0 10px;">
                <label class="label">Video Name</label>
                <p class="control">
                    <input class="input" value="{{$course_video?$course_video->name:''}}" name="course_video_name" type="text" placeholder="Video Name">
                </p>
            </div>
            <div class="field" style="margin-top:10px;padding:0 10px;">
                <label class="label">Video Description</label>
                <p class="control">
                    <textarea class="textarea" value="{{$course_video?$course_video->description:''}}" name="course_video_description" placeholder="Video Description">{{$course_video?$course_video->description:''}}</textarea>
                </p>
            </div>
            <div class="field" style="margin-top:10px;padding:0 10px;">
                <div class="file has-name is-boxed">
                    <label class="file-label">
                        <input class="file-input" accept="video/*" type="file" name="video_file">
                        <span class="file-cta">
                        <span class="file-icon">
                            <i class="fas fa-upload"></i>
                        </span>
                        <span class="file-label">
                            Choose a file…
                        </span>
                        </span>
                        <span class="file-name">
                        {{$course_video?$course_video->video:''}}
                        </span>
                    </label>
                </div>
                <label class="label" style="margin:10px 5px;">Video URL</label>
                <p class="control" style="margin:10px 5px;">
                    <input class="input"
                    {{$type=="Add"?'disabled':''}}
                    value="{{$course_video?$course_video->video_url:''}}" name="course_video_video_url" type="text" placeholder="Video URL">
                </p>
            </div>
            <button type="submit" class="button is-primary is-rounded">
                Save
            </button>
        </form>
    </div>
</div>
@endsection