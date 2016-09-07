@extends('layouts.app')

@section('content')

    <div id="checklist-all" class="container">
        <h3 class="page-title">
            <strong>Your Checklists</strong>
        </h3>
        <p>Hi {{ Auth::user()->name }}, here are all the checklists you've made with us.</p>
        <hr>

        <div>
            <a href="/checklist/make">
                <button type="button" class="btn btn-primary">New Checklist</button>
            </a>
        </div>

        <br>


        <my-checklists></my-checklists>
    </div>

@endsection