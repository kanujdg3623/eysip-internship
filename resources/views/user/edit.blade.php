@extends('layouts.app')

@section('content')
<br>
<div class="columns is-marginless is-centered">
    <div class="column is-4">
        <div class="card ">
           <!-- <header class="card-header">
                <p class="card-header-title">Register</p>
            </header> -->

            <div class="card-content is-success">
                    <form method="POST" action="{{ route('user.update') }}" enctype="multipart/form-data" >
                        @csrf
				        @method('PUT')
                        
                        <div class="field">
						  <label class="label">Name</label>
						  <div class="control has-icons-left has-icons-right">
							<input class="input is-rounded @if ($errors->has('name')) is-danger @endif" type="text" placeholder="Name" name="name" value="{{ old('title') ?? Auth::user()->name}}">
							<span class="icon is-small is-left">
							  <i class="fas fa-user"></i>
							</span>						
						  @if ($errors->has('name'))<span class="icon is-small is-right">
							  <i class="fas fa-exclamation-triangle"></i>@endif
							</span>
						  </div>
						  @if ($errors->has('name'))
								<p class="help is-danger">
									{{ $errors->first('name') }}
								</p>
							@endif
						</div>                      
                        
                        <div class="field">
						  <label class="label">Contact number</label>
						  <div class="control has-icons-left has-icons-right">
							<input class="input is-rounded @if ($errors->has('contact_number')) is-danger @endif" type="number" placeholder="Contact Number" name="contact_number" value="{{ old('contact_number') ??  Auth::user()->contact_number}}">
							<span class="icon is-small is-left">
							  <i class="fas fa-phone"></i>
							</span>						
						  @if ($errors->has('contact_number'))<span class="icon is-small is-right">
							  <i class="fas fa-exclamation-triangle"></i>@endif
							</span>
						  </div>
						  @if ($errors->has('contact_number'))
								<p class="help is-danger">
									{{ $errors->first('contact_number') }}
								</p>
							@endif
						</div>
		                </br>
                		
                		<div id="file-js-example" class="field">
							<label class="label">Profile image</label>					
						  <div class="control has-icons-right">
							  <label class="file-label">
								<input class="file-input" type="file" name="image" onchange="
								 const fileInput = document.querySelector('#file-js-example input[type=file]');
								 if (fileInput.files.length > 0) {
								 const fileName = document.querySelector('#file-js-example .file-name');
								  fileName.textContent = fileInput.files[0].name; }
								 ">
								<span class="file-cta">
								  <span class="file-icon">
									<i class="fas fa-upload"></i>
								  </span>
								  <span class="file-label">
									Choose an image…
								  </span>
								</span>
								<span class="file-name">
								  No image uploaded
								</span>
							  </label>
							  @if ($errors->has('image'))
				                <p class="help is-danger">
				                    {{ $errors->first('image') }}
				                </p>
				            @endif
						  </div>
						</div>
		                <br>

                         <button type="submit" class="button is-primary is-fullwidth is-rounded">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
