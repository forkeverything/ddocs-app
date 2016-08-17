@extends('emails.partials.standard')

@section('header-color', '#2ECC71')
@section('header-text', 'Free Credits')
@section('subheading', 'Woo!')
@section('summary')You've just received 10 Free Credits.@endsection
@section('body')
    <div>
        {{ $recipient->name }} ({{ $recipient->email }}) has signed up. And copped you both bonus credits. Remember, credits never expire so keep on creating lists to keep on earning!
    </div>
@endsection
@section('button-link'){{ env('DOMAIN') }}/checklist/make@endsection
@section('button-text', 'New Checklist')