@extends('layouts.app')
@section('content')
    <div id="projects-start" class="container">
        <h3 class="text-center">Start A Project</h3>
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <form action="/projects" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="input-project-name" class="text-center show">What's the name of your project?</label>
                        <input id="input-project-name" type="text" class="form-control input-lg text-center" name="name">
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-lg btn-success">Create Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection