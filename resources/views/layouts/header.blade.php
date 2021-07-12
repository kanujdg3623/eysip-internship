<header>
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="{{ route('landing') }}">
            <img src="{{asset('eYantra_logo.svg')}}" alt="e-Yantra Logo" width="112" height="28">
            </a>

            <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="nav-menu" onclick="this.classList.toggle('is-active');document.getElementById('nav-menu').classList.toggle('is-active');">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            </a>
        </div>

        <div id="nav-menu" class="navbar-menu">
            <div class="navbar-start">
                <a class="navbar-item is-active">
                    Home
                </a>
                
                <a class="navbar-item" href="{{ route('course.index') }}">
                    Courses
                </a>

                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                    Initiatives
                    </a>

                    <div class="navbar-dropdown">
                        <a class="navbar-item" href="{{ route('videos.index', 'eYRC') }}">
                            eYRC
                        </a>
                        <a class="navbar-item" href="{{ route('videos.index', 'eYIC') }}">
                            eYIC
                        </a>
                        <a class="navbar-item" href="{{ route('videos.index', 'eLSI') }}">
                            eLSI
                        </a>
                        <a class="navbar-item" href="{{ route('videos.index', 'eYSIP') }}">
                            eYSIP
                        </a>
                        <a class="navbar-item" href="{{ route('videos.index', 'eFSI') }}">
                            eFSI
                        </a>
                        <a class="navbar-item" href="{{ route('videos.index', 'eYS') }}">
                            eYS
                        </a>
                        <a class="navbar-item" href="{{ route('videos.index', 'e-Yantra Impact') }}">
                            e-Yantra Impact
                        </a>
                        <a class="navbar-item" href="{{ route('videos.index', 'Hackathon') }}">
                            Hackathon
                        </a>
                        <a class="navbar-item" href="{{ route('videos.index', 'Talk') }}">
                            Talk
                        </a>
                        <a class="navbar-item" href="{{ route('videos.index', 'Workshop') }}">
                            Workshops
                        </a>
                    </div>
                </div>
                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                    More
                    </a>

                    <div class="navbar-dropdown">
                        <a class="navbar-item">
                            Publication
                        </a>
                        <a class="navbar-item">
                            Jobs
                        </a>
                        <a class="navbar-item">
                            News
                        </a>
                    </div>
                </div>
            </div>

            <div class="navbar-end">
                @guest
                    <div class="buttons">
                        <a class="button login is-text" href="{{ route('login') }}">
                            <strong>Login</strong>
                        </a>
                        @if (Route::has('register'))
                        <a class="button register is-text" href="{{ route('register') }}">
                            <strong>Register</strong>
                        </a>
                        @endif
                    </div>
                @else
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link" href="#">{{ Auth::user()->name }}</a>

                        <div class="navbar-dropdown" style="right:0;left:auto">
                            @if(Auth::user()->post)
                                <a class="navbar-item" href="{{ route('admin-course.index') }}">
                                    Courses
                                </a>
                                <hr class="navbar-divider">
                                <a class="navbar-item" href="{{ route('admin.visitor-analysis') }}">
                                    Visitor Analysis
                                </a>
                                <a class="navbar-item" href="{{ route('admin.client-analysis') }}">
                                    Client Analysis
                                </a>
                                <a class="navbar-item" href="{{ route('admin.youtube-analysis') }}">
                                    Youtube Analysis
                                </a>
                                <a class="navbar-item" href="{{ route('admin.youtube') }}">
                                    eYantra videos
                                </a>
                                <a class="navbar-item" href="{{ route('admin.initiatives','read') }}">
                                    Initiatives
                                </a>
                                <hr class="navbar-divider">
                                <a class="navbar-item" href="{{ route('admin.logout') }}"
                                    onclick="event.preventDefault();
                                                    document.getElementById('user-logout-form').submit();">
                                    Logout
                                </a>
                                <form id="user-logout-form" action="{{ route('admin.logout') }}" method="GET" style="display: none;">
                                    @csrf
                                </form>
                            @else
                                <a class="navbar-item" href="{{ route('user.logout')}}"
                                    onclick="event.preventDefault();
                                                    document.getElementById('user-logout-form').submit();">
                                    Logout
                                </a>
                                <form id="user-logout-form" action="{{ route('user.logout') }}" method="GET" style="display: none;">
                                    @csrf
                                </form>
                            @endif
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </nav>
</header>
