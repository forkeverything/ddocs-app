@extends('emails.partials.standard')

@section('header-color', '#2ECC71')
@section('header-text', 'Account Created')
@section('subheading')Hi {{ $user->name }},@endsection
@section('summary')
    You've recently created a checklist with us via email. Here are your account details.
@endsection
@section('body')
    <div>
        <strong>User / Email</strong>
        <br>
        <p>
            {{ $user->email }}
        </p>
        <br>
        <strong>Password</strong>
        <br>
        <p>
            {{ $password }}
        </p>
    </div>
@endsection
@section('button-link'){{ env('DOMAIN') }}/checklist/@endsection
@section('button-text', 'Your Checklists')