@extends('layouts.app')

@section('content')
    <div id="checklist-single" class="container">

        @if(!Auth::check() && ! $checklist->invitation_claimed)
            <div id="join-offer">
                <h3 class="text-center">Help {{ $checklist->user->name }} Out!</h3>
                <p class="text-center">Join Files Collector and give {{ $checklist->user->name }} 10 free credits.
                    You'll also get 5 bonus credits for being an awesome friend.
                    <br>
                    Think of it as our way of saying thanks for trying us out!
                </p>
                <a href="/register?invite_key={{ $checklistHash }}"><p class="text-center">Sign Me Up</p></a>
            </div>
        @endif

        <div id="header">
            @if(Auth::check() && $checklist->madeBy(Auth::user()))
                <ol class="breadcrumb">
                    <li><a href="/checklist">My Lists</a></li>
                    <li class="active">{{ $checklist->name }}</li>
                </ol>
            @endif
            @if(! Auth::check() || ! $checklist->madeBy(Auth::user()))
                    <div class="right">
                        <checklist-notifications-control :user="{{ Auth::user() }}" :recipient-notifications="{{ $checklist->recipient_notifications }}" :checklist-hash="'{{ $checklistHash }}'"></checklist-notifications-control>
                    </div>
            @endif
        </div>

        <h1 class="text-center text-capitalize">
            {{ $checklist->name }}
        </h1>
        @if($checklist->description)
            <p class="text-center">{{ $checklist->description }}</p>
        @endif
        <br>

            <checklist-file-requests :checklist-hash="'{{ $checklistHash }}'" :can-upload="{{Auth::guest() || $checklist->user_id !== Auth::user()->id}}"></checklist-file-requests>

    </div>
@endsection