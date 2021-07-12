@extends('layouts.app')

@section('content')
<div class="create-courses">
    <section class="hero is-small is-light is-bold">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                {{$type}} Course
                </h1>
            </div>
        </div>
    </section>
    <div class="container">
        <form action="{{ route('admin-course.store') }}" method="POST">
            @csrf
            <input hidden value="{{$type}}" name="type" type="text" placeholder="Form Type">
            <input hidden value="{{$course?$course->id:''}}" name="course_id" type="text" placeholder="Course Id">
            <div class="field" style="margin-top:10px;padding:0 10px;">
                <label class="label">Course Name</label>
                <p class="control">
                    <input class="input" value="{{$course?$course->name:''}}" name="course_name" type="text" placeholder="Course Name">
                </p>
            </div>
            <div class="field" style="margin-top:10px;padding:0 10px;">
                <label class="label">Course Skillset</label>
                <p class="control">
                    <textarea class="textarea" value="{{$course?$course->skillset:''}}" name="course_skillset" placeholder="Course Skillset">{{$course?$course->skillset:''}}</textarea>
                </p>
            </div>
            <div class="field" style="margin-top:10px;padding:0 10px;">
                <label class="label">Course Description</label>
                <p class="control">
                    <textarea class="textarea" value="{{$course?$course->description:''}}" name="course_description" placeholder="Course Description">{{$course?$course->description:''}}</textarea>
                </p>
            </div>
            <button type="submit" class="button is-primary is-rounded">
                Save
            </button>
        </form>
    </div>
</div>
@endsection