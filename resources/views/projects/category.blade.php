@extends('layouts.app')
@section('content')
    <div id="projects-single">
        <h3>
            <span class="small">project</span>
            <br>
            {{ $project->name }}
        </h3>

        <project-items :parent="{{ $projectCategory }}"></project-items>

    </div>
@endsection