@extends('layouts.app')
@section('content')
    <div id="projects-all" class="container">
        <h3>Projects</h3>
        <div class="row">
            <div class="col-sm-5">
                <p class="small text-muted">Projects are used to keep track of your internal team's files. See what
                    members of your team are working on, view progress and file statuses at a glance.</p>
            </div>
        </div>
        <br>
        <div id="create-project" class="dropdown">
            @include('errors.list')
            <button type="button" class="btn btn-info" data-toggle="dropdown">New Project</button>
            <form id="form-create-project" action="/projects" method="POST" class="dropdown-menu">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="">Name</label>
                    <input type="text" name="name" v-el:input-name class="form-control">
                </div>
                <div class="form-group">
                    <label for="">Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-success btn-sm">Create</button>
                </div>
            </form>
        </div>
        <br>
        @if($projects)
            <ul id="projects-list" class="list-unstyled">
                @foreach($projects as $project)
                    <li><a href="/projects/{{ $project->id }}">{{ $project->name }}</a></li>
                @endforeach
            </ul>
        @else
            <p>You haven't created any projects.</p>
        @endif
    </div>
@endsection