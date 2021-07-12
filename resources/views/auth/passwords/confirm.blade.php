@extends('layouts.app')

@section('content')
<section class="section" align="center">
  </section>
<div class="columns is-marginless is-centered">
	<div class="column is-4">
		<div class="card">
			<header class="card-header">
				<p class="card-header-title">
				  {{ __('Please confirm your password before continuing.') }}
				</p>
			</header>
			<div class="card-content">		
				<form method="POST" action="{{ route('password.confirm') }}">
    	            @csrf
    	            <div class="field">
    	            	<div class="control">
			                <input type="password" class="input @if ($errors->has('password')) is-danger @endif" type="text" placeholder="Password" name="password">
		               	</div>		               	
						@if ($errors->has('password'))
							<p class="help is-danger">
								{{ $errors->first('password') }}
							</p>
						@endif
	                </div>
					<button type="submit" class="button is-primary is-fullwidth is-rounded">{{ __('Confirm Password') }}</button>
					@if (Route::has('password.request'))
					<br>
						<div align="center">
							<p class="help is-link">
								<a href="{{ route('password.request') }}">
									Forgot Your Password?
								</a>
							</p>
						</div>
					@endif
                </form>
			</div>
		</div>
	</div>
</div>
@endsection
