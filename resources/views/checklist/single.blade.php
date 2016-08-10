@extends('layouts.app')

@section('content')
    <checklist-single inline-template :checklist-hash="'{{ $checklistHash }}'" v-cloak>
        <div id="checklist-single" class="container">


            @if(Auth::check() && $checklist->madeBy(Auth::user()))
            <ol class="breadcrumb">
                <li><a href="/checklist">My Lists</a></li>
                <li class="active">{{ $checklist->name }}</li>
            </ol>
            @endif

            <h1 class="text-center text-capitalize">
                {{ $checklist->name }}
            </h1>
            @if($checklist->description)
                <p class="text-center">{{ $checklist->description }}</p>
            @endif
            <br>

            @include('checklist.single.search')
            @include('checklist.single.filters.active')

            <div class="progress">
                <div class="progress-bar" role="progressbar" :aria-valuenow="receivedFilesPercentage" aria-valuemin="0" aria-valuemax="100" :style="'width: ' + receivedFilesPercentage + '%' + ';min-width: 2em;'">
                    @{{ receivedFilesPercentage }}% Received
                </div>
            </div>

            @include('checklist.single.table-files')
        </div>
    </checklist-single>
@endsection