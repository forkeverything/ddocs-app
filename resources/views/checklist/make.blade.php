@extends('layouts.app')

@section('content')
    <checklist-make inline-template v-cloak>
        <div id="checklist-make" class="container">
            <ol class="breadcrumb">
                <li><a href="/checklist">My Lists</a></li>
                <li class="active">New Checklist</li>
            </ol>

            <h1 class="text-center">New Checklist</h1>
            <p class="text-center">Create a list of all the files that you need along with their due dates (if any) and we'll handle the rest.</p>
            <br>




            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <form-errors></form-errors>
                    @include('checklist.make.form')
                    <div class="text-right">
                        <button type="button" class="btn btn-solid-green" @click="sendChecklist" :disabled="! canSendChecklist">@{{ makeListButtonText }}</button>
                    </div>
                </div>
            </div>
        </div>
    </checklist-make>
@endsection
