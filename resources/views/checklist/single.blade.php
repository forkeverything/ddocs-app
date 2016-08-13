@extends('layouts.app')

@section('content')
    <checklist-single inline-template :checklist-hash="'{{ $checklistHash }}'" :recipient-notifications="{{ $checklist->recipient_notifications }}" v-cloak>
        <div id="checklist-single" class="container">

            <div id="header">
                @if(Auth::check() && $checklist->madeBy(Auth::user()))
                    <ol class="breadcrumb">
                        <li><a href="/checklist">My Lists</a></li>
                        <li class="active">{{ $checklist->name }}</li>
                    </ol>
                @endif
                @if(! Auth::check() || ! $checklist->madeBy(Auth::user()))
                    <div class="right">
                        <button v-show="recipientNotifications" type="button" class="btn btn-text" @click="toggleConfirmTurnOffNotifications" v-show="! showTurnOffNotificationsPanel">Turn Off Notifications</button>
                        <p v-else class="text-muted">Notifications & Reminders Disabled</p>
                    </div>
                @endif
            </div>

            <form id="form-notifications" v-show="showTurnOffNotificationsPanel" @submit.prevent="turnOffNotifications">
                <h4>Turn Off  Email Notifications</h4>
                <form-errors></form-errors>
                <p class="text-muted">Please enter your email address</p>
                <div class="form-group email-field">
                    <input type="email" class="form-control" placeholder="you@example.com" name="email" v-model="emailToTurnOffNotifications">
                </div>
                <button type="button" class="btn btn-solid-grey btn-space" @click="toggleConfirmTurnOffNotifications">Cancel</button>
                <button type="submit" class="btn btn-solid-red">Stop reminders for this checklist</button>
            </form>

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
                <div class="progress-bar" role="progressbar" :aria-valuenow="receivedFilesPercentage" aria-valuemin="0"
                     aria-valuemax="100" :style="'width: ' + receivedFilesPercentage + '%' + ';min-width: 2em;'">
                    @{{ receivedFilesPercentage }}% Received
                </div>
            </div>

            @include('checklist.single.table-files')
        </div>
    </checklist-single>
@endsection