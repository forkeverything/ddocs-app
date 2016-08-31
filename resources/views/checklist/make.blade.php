@extends('layouts.app')

@section('content')
    <div id="checklist-make" class="container">

        <ol class="breadcrumb">
            <li><a href="/checklist">My Lists</a></li>
            <li class="active">New Checklist</li>
        </ol>

        <h3><strong>New Checklist</strong></h3>

        <p>
            Create a list of all the files that you need along with their due dates (if any) and
            we'll handle the rest.
        </p>

        <br>

        <checklist-maker :user="{{ Auth::user() }}"></checklist-maker>


    </div>
@endsection
