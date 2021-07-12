@extends('layouts.app')

@section('content')
<analytics id='{{$id}}' title='{{$title}}'></analytics>

@endsection

@section("component-script")
<script src="{{ asset('js/chartApp.js') }}" defer></script>
<script src="{{ asset('js/vueApp.js') }}" defer></script>
@endsection
