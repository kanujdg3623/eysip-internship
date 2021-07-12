@extends('layouts.app')

@section('content')
<div class="container">
    <div class="columns is-marginless is-centered">
        <div class="column is-5">
            <nav class="card">
            	<header class="card-header">
						<p class="card-header-title">{{Auth::user()->name}} Dashboard</p>
						<a href="{{route('password.confirm')}}" class="card-header-icon" aria-label="more options">
							<span class="icon">
								<i class="fas fa-user-edit" aria-hidden="true"></i>
						  </span>
						</a>
            	</header>
            	<br>
                <div class="card-content">
                	<div class="media">
                		<div class="media-left">
		        			<figure class="image is-128x128">
								<img class="is-rounded" src="{{ Auth::user()->profileImage()}}">
							</figure>
                		</div>
                		<div class="media-content">
                			<p class="title is-4">{{Auth::user()->name}}</p>
                			<p class="subtitle is-6">{{Auth::user()->gender}}</p>
                			<p class="subtitle is-6">{{Auth::user()->email}}<br>{{Auth::user()->contact_number}}</p>
                		</div>
                	</div>
                </div>
            </nav>
        </div>
    </div>
</div>

@endsection
