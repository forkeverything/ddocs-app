@extends('layouts.app')
@section('content')
    <div id="projects-single">
        <h3>
            <span class="small">project</span>
            <br>
            {{ $project->name }}
        </h3>
        <project-board :project="{{ $project }}"></project-board>
    </div>
@endsection