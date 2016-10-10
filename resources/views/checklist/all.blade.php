@extends('layouts.app')

@section('content')

    <div id="checklist-all" class="container-fluid">
        <div class="header">
            <h3>
                Your Checklists
            </h3>
            <a href="/c/make">
                <button type="button" class="btn btn-info btn-sm">New Checklist</button>
            </a>
        </div>
        <br>
        <checklist-collection></checklist-collection>
    </div>
@endsection