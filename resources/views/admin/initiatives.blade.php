@extends('layouts.app')

@section('content')
	<initiatives v-bind:table='{{$table}}'></initiatives>

@endsection

@section("component-script")
<script src="{{ asset('js/vueApp.js') }}" defer></script>
@endsection