<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @yield("component-meta")

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Scripts -->
    <script async defer src="https://apis.google.com/js/api.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
	
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/vue.css') }}" rel="stylesheet">
    
    <style>
        :root {
        --primary-text-bg-1: #00D1B2;
        --primary-text-bg-2: #fff;
        --primary-text-bg-3: #eee;
        --primary-text-bg-4: #666;
        --accent-1: #212121;
        --accent-2: #455A64;
        --accent-3: #78909C;
        --accent-4: #7c7ce0;
        --link-1: #DB4437;
        --link-2: #F6CD4C;
      }
      .initiative-element{
        margin: 10px 20px;
      }
      .carousel-images .slider-navigation-previous,
      .carousel-images .slider-navigation-next,
      .carousel-images .slider-pagination{
        display:none;
      }
    </style>
    @yield("component-styles")
</head>
<body>
    <div id="app">
        @include("layouts.header")
        
        @if (session('status'))
          <div class="container notification is-warning is-light" align="center">
            {{ session('status') }}
          </div>
        @endif
		
		
        <main class="py-4">
            @yield('content')
        </main>

        @include("cookies.cookies")
    </div>
</body>
<script src="{{ asset('js/app.js') }}" defer></script>
@yield("component-script")
<!-- Cookie script -->
@include("cookies.cookies-script")
</html>
