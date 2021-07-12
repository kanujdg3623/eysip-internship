@extends('layouts.app')

@section('content')
<div class="courses">
<section class="hero is-small is-light is-bold">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
            e-Yantra Courses
            </h1>
        </div>
    </div>
</section>
<div class="container">
    @if(Auth::user()->post)
    <form class="search-courses" method="GET" action="{{ route('admin-course.index') }}">
    @else
    <form class="search-courses" method="GET" action="{{ route('course.index') }}">
    @endif
        <div class="field" style="margin-top:10px;padding:0 10px;">
            <p class="control has-icons-left has-icons-right">
                <input class="input" name="course_search" type="text" value="{{ $course_search }}" placeholder="Search Courses">
                <span class="icon is-small is-left">
                    <i class="fas fa-search"></i>
                </span>
            </p>
        </div>

        <button type="submit" class="button is-primary is-rounded">
            Search
        </button>
        @if(Auth::user()->post)
            <a href="{{ route('admin-course.create') }}" class="button is-primary is-rounded">
                Create
            </a>
        @endif
    </form> 
    <div class="columns is-multiline" style="margin-top:10px;padding:0 10px;">
        @foreach($courses as $course)
            <div class="column is-half-tablet is-one-quarter-desktop fade">
                <div class="card">
                    <div class="card-image">
                        <figure class="image is-4by3">
                            <img src="https://bulma.io/images/placeholders/1280x960.png" alt="Placeholder image">
                        </figure>
                    </div>
                    <div class="card-content">
                        <div class="media">
                            <div class="media-content">
                                <p class="title is-4">{{ $course->name }}</p>
                            </div>
                        </div>
                        <div class="content">
                            <br>
                            Skill Set: <strong>{{ $course->skillset }}</strong>
                        </div>
                    </div>
                    @if(Auth::user()->post)
                        <footer class="card-footer">
                            <a href="{{ route('admin-course.show', $course->id) }}" class="card-footer-item">Go To Course</a>
                        </footer>
                    @else
                        <footer class="card-footer">
                            <a href="{{ route('course.show', $course->id) }}" class="card-footer-item">Go To Course</a>
                        </footer>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
</div>
@endsection