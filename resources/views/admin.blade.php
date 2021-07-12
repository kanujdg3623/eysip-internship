@extends('layouts.app')

@section('content')
<div class="container">
    <div class="columns is-marginless is-centered">
        <div class="column is-7">
            <nav class="card">
            	<header class="card-header">
						<p class="card-header-title">{{Auth::user()->name}} Dashboard</p>
						<a href="{{route('admin.edit')}}" class="card-header-icon" aria-label="more options">
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
                			<p class="subtitle is-6">{{Auth::user()->post}}</p>
                			<p class="subtitle is-6">{{Auth::user()->email}}<br>{{Auth::user()->contact_number}}</p>
                		</div>
                	</div>
                </div>
            </nav>
        </div>
    </div>
</div>
<div class="container">
    <div class="columns is-marginless is-centered">
        <div class="column is-7">
            <div class="card">
            	<header class="card-header">
            		<p class="card-header-title">
						New Registrations
					</p>
				</header>
            	<div class="card-content">
				{{$new->links()}}
            	@foreach($new as $n)
					<div class="columns">
						<div class="column">
							<p class="title is-5">{{$n->name}}</p>
							<p class="subtitle is-6">{{$n->email}}</p>
						</div>
						<div class="column has-text-right">
							<small>{{Carbon\Carbon::parse($n->created_at)->diffForHumans()}}</small>
							<form method="get" action="/admin/approve/{{$n->id}}">
								<input class="button is-primary is-light" type="submit" name="prompt" value="Accept">
								<input class="button is-danger is-light" type="submit" name="prompt" value="Decline">
							</form>
						</div>
					</div>     	
            	@endforeach
            	</div>
            </div>
        </div>
    </div>
</div>
@endsection
