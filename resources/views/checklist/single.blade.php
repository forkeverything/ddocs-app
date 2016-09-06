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


        @if(Auth::check() && $checklist->madeBy(Auth::user()))
            <ol class="breadcrumb">
                <li><a href="/checklist">My Lists</a></li>
                <li class="active">{{ $checklist->name }}</li>
            </ol>
        @endif


        <h3>
            <strong>
                <span class="text-capitalize">{{ $checklist->name }}</span>
            </strong>
        </h3>

            <div class="recipients">
                <span class="span-to">To: </span>
                <ul class="recipients-list list-unstyled list-inline">
                    @foreach($checklist->recipients as $recipient)
                        <li>{{ $recipient->email }}</li>
                    @endforeach
                </ul>
            </div>

        @if($checklist->description)
            <p>{{ $checklist->description }}</p>
        @endif

        <br>

        <checklist-file-requests :checklist-hash="'{{ $checklistHash }}'"
                                 :is-owner="{{ $checklist->madeBy(Auth::user()) }}"
                                 :aws-url="'{{ awsURL() }}'"></checklist-file-requests>

    </div>
@endsection