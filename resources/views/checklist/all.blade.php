@extends('layouts.app')

@section('content')

    <div id="checklist-all" class="container">
        <div class="header">
            <h3>
                Your Checklists
            </h3>
            <div>
                <a href="/c/make">
                    <button type="button" class="btn btn-info">New Checklist</button>
                </a>
            </div>
        </div>
        <br>
        <checklist-collection></checklist-collection>
    </div>
@endsection