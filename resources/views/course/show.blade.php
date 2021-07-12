@extends('layouts.app')

@section('content')
<div class="course-content">
    @if(session('message'))
    <div class="notification is-success is-light" id="CourseMessage">
        {{session('message')}}
    </div>
    @endif
    <section class="section">
        <div class="container">
            <h1 class="title is-2">{{$course->name}}</h1>
            <h2 class="subtitle">
                {{$course->description}}
            </h2>
            <p>
            Skill Set: <strong>{{ $course->skillset }}</strong>
            </p>
            <div class="content" style="margin-top:10px;padding:0 10px;">
                @if(Auth::user()->post)
                    <a href="{{ route('admin-course.edit', $course->id) }}" class="button is-primary is-rounded">
                        Edit
                    </a>
                    <a href="{{ route('admin-course-video.create', $course->id) }}" class="button is-primary is-rounded">
                        Add Videos
                    </a>
                @else
                    <a href="#" class="button is-primary is-rounded">
                        Enroll
                    </a>
                @endif
            </div>
        </div>
    </section>
     <coursevideos v-bind:course_videos='{{$course_videos}}'></coursevideos>
</div>
@endsection

@section("component-script")
<script src="{{ asset('js/vueApp.js') }}" defer></script>
@endsection