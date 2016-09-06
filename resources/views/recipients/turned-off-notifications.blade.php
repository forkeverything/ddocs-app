@extends('layouts.app')
@section('content')
    @if($recipient)
        <h3 class="text-center" style="padding: 3em 0;">Succesfully turned off notifications.</h3>
    @else
        <h3 class="text-center" style="padding: 3em 0;">Sorry, we couldn't find your email in our list. Please check the URL and try again.</h3>
    @endif
@endsection