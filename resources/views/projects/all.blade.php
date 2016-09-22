@extends('layouts.app')
@section('content')
    <div id="projects-all" class="container">
        <div class="header">
            <h3>Projects</h3>
                <a href="/projects/start" class="btn btn-primary btn-sm">Start New Project</a>
        </div>
        @if(count($projects))
            <ul id="list-projects" class="list-unstyled">
                @foreach($projects as $project)
                    <li><a href="/projects/{{ $project->id }}">{{ $project->name }}</a></li>
                @endforeach
            </ul>
        @else
            <p class="small">You haven't started any projects.</p>
        @endif
    </div>
@endsection