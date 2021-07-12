@extends('layouts.app')

@section('content')
<br>
<section class="hero is-small is-light is-bold">
    <div class="hero-body">
        <div class="container">
        @if ($initiative == 'eYRC')
            <h1 class="title">
                e-Yantra Robotics Competition (eYRC)
            </h1>
            <h2 class="subtitle">
                Glimpses of eYRC
            </h2>
        @elseif ($initiative == 'eYIC')
            <h1 class="title">
                e-Yantra Ideas Competition (eYIC)
            </h1>
            <h2 class="subtitle">
                Glimpses of eYIC
            </h2>
        @elseif ($initiative == 'eYSIP')
            <h1 class="title">
                e-Yantra Summer Internship Program (eYSIP)
            </h1>
            <h2 class="subtitle">
                Glimpses of eYSIP
            </h2>
        @elseif ($initiative == 'eLSI')
            <h1 class="title">
            	e-Yantra Lab Setup Initiative (eLSI)
            </h1>
            <h2 class="subtitle">
                Glimpses of eLSI
            </h2>
        @elseif ($initiative == 'e-Yantra Impact')
            <h1 class="title">
            	e-Yantra Impact
            </h1>
            <h2 class="subtitle">
            </h2>
        @elseif ($initiative == 'Hackathon')
            <h1 class="title">
            	e-Yantra Hackathon
            </h1>
        @elseif ($initiative == 'Talk')
            <h1 class="title">
            	e-Yantra Tech Talk
            </h1>
            <h2 class="subtitle">
                Glimpses of Talks
            </h2>
        @elseif ($initiative == 'Workshop')
            <h1 class="title">
            	e-Yantra Workshop
            </h1>
            <h2 class="subtitle">
                Glimpses of Workshops
            </h2>
        @elseif ($initiative == 'eYS')
            <h1 class="title">
            	e-Yantra Symposium (eYS)
            </h1>
            <h2 class="subtitle">
                Glimpses of eYS
            </h2>
        @elseif ($initiative == 'eFSI')
            <h1 class="title">
            	e-Yantra Farm Setup Initiative (eFSI)
            </h1>
            <h2 class="subtitle">
                Glimpses of eFSI
            </h2>
        @endif
        </div>
    </div>
</section>
<youtube initiative='{{$initiative}}'></youtube>

@endsection

@section("component-script")
<script src="{{ asset('js/vueApp.js') }}" defer></script>
@endsection
