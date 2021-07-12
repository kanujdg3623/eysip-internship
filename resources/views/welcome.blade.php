<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Welcome to e-Yantra!</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <style>
        body {         
            margin: 0;
        }
            
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
        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: block;
                justify-content: left;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: left;
            }

            .content .title {
                font-size: 54px;
                padding: 85px 25px 25px;
                font-style: italic;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .versioninfo {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
            }

            .framwork_title {
                font-weight: 600;
                padding-top: 20px;
            }

            .m-b-md {
                margin-bottom: 30px;
                margin-left: 30px;
            }
            .eY-home{
                background-image: url("{{ asset('illustration.jpg') }}");
                background-repeat: no-repeat;
                background-attachment: scroll;
                background-position: center; 
                background-size: cover;
                position:relative;
                top:0;
                left:0;
                width:100vw;
                height:100vh;
            }
        </style>
    </head>
    <body>
        @include("layouts.header")
        <div class="flex-center position-ref full-height eY-home">

            <div class="content">
                <div class="title m-b-md">
                	
                    "Building Innovation <br/>Eco-Systems in Colleges"
                </div>
                <div>
                    
                </div>

                <!-- <div class="links">
                    <a href="https://laravel.com/docs">Documentation</a>
                    <a href="https://laracasts.com">Laracasts</a>
                    <a href="https://laravel-news.com">News</a>
                    <a href="https://forge.laravel.com">Forge</a>
                    <a href="https://github.com/laravel/laravel">GitHub</a>
                </div>

                <div class="foundation_button_test">
                    <p class="framwork_title">Bulma v0.7.4</p>
                    <p class="framwork_title">Bulma Extension v4.0.2</p>

                    <div class="block">
                        <a class="button is-primary">Primary</a>
                        <a class="button is-info">Info</a>
                        <a class="button is-success">Success</a>
                        <a class="button is-warning">Warning</a>
                        <a class="button is-danger">Danger</a>
                    </div>
                </div> -->
            </div>
        </div>
        @include("initiative-cards.initiative-cards")
        @include("cookies.cookies")
        
    </body>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bulma-carousel@4.0.4/dist/js/bulma-carousel.min.js"></script>
    @include("cookies.cookies-script")
    @include("initiative-cards.initiative-cards-script")
</html>

