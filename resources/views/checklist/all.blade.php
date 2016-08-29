@extends('layouts.app')

@section('content')

    <div id="checklist-all" class="container">
        <h1 class="text-center">My Lists</h1>
        <p class="text-center">Hi {{ Auth::user()->name }}, here are all the checklists you've made with us.</p>
        <br>

        <div class="form-group text-right">
            <a href="/checklist/make">
                <button type="button" class="btn btn-solid-green">New Checklist</button>
            </a>
        </div>

        <my-checklists></my-checklists>
    </div>

@endsection