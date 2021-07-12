@extends('layouts.app')

@section('content')


<section class="section" align="center">
  </section>
<div class="columns is-marginless is-centered">
	<div class="column is-4">
    	<div class="card">
    		<br><br>
    		<div class="card-image" align="center">
				<figure class="image is-128x128">
				  <img src="/User_icon.png" alt="Placeholder image">
				</figure>
				<br>
			  <div class="content"><h3>Account Login</h3></div>
			  </div>
            <div class="card-content">            	
                <form class="login-form" method="POST" action="{{ route('check') }}">
                    {{ csrf_field() }}
                    
                    <div class="field">
					  <p class="control has-icons-left has-icons-right">
						<input class="input is-rounded @if ($errors->has('email')) is-danger @endif" type="email" name="email" placeholder="Email">
						<span class="icon is-small is-left">
						  <i class="fas fa-envelope"></i>
						</span>
						@if ($errors->has('email'))
							<span class="icon is-small is-right">
							  <i class="fas fa-exclamation-triangle"></i>
							</span>
                            <p class="help is-danger">
								{{ $errors->first('email') }}
							</p>
                        @enderror
						
					  </p>
					</div>
					
					<div class="field">
					  <p class="control has-icons-left has-icons-right">
						<input class="input is-rounded @if ($errors->has('password')) is-danger @endif" type="password" name="password" placeholder="Password">
						<span class="icon is-small is-left">
						  <i class="fas fa-lock"></i>
						</span>
						@if ($errors->has('password'))
							<span class="icon is-small is-right">
							  <i class="fas fa-exclamation-triangle"></i>
							</span>
                            <p class="help is-danger">
								{{ $errors->first('password') }}
							</p>
                        @enderror
					  </p>
					</div>

					<button type="submit" class="button is-primary is-fullwidth is-rounded">
					  Login
					</button>
					<br>
					<div align="center">
						<p class="help is-link">
                        <a href="{{ route('password.request') }}">
                            Forgot Your Password?
                        </a>
                        </p>
                    </div>
                </form>
                <br><br>
            </div>
        </div>
    </div>
</div>

@endsection
