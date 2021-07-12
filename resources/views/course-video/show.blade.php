@extends('layouts.app')

@section('content')
<div class="course-content">
    @if(session('message'))
    <div class="notification is-success is-light" id="CourseVideoMessage">
        {{session('message')}}
    </div>
    @endif
    <section class="section">
        <div class="container">
            <h1 class="title is-2">{{$course_video->name}}</h1>
            <h2 class="subtitle">
                {{$course_video->description}}
            </h2>
            <div class="content" style="margin-top:10px;padding:0 10px;">
                @if(Auth::user()->post)
                    <a href="{{ route('admin-course-video.edit', ['id' => $course_video->course_id, 'video_id'=>$course_video->id]) }}" class="button is-primary is-rounded">
                        Edit
                    </a>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
