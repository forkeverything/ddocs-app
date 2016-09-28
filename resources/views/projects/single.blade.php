@extends('layouts.app')
@section('content')
    <div id="project-single">

        <div id="project-info" class="container-fluid">
            <h3>
                <small>Project Files</small>
                <br>
                {{ $project->name }}
            </h3>
            @if($project->description)
                <p class="small text-muted">
                    {{ $project->description }}
                </p>
                <br>
            @endif
        </div>
        <div class="board-wrap">
            <project-board :project="{{ $project }}"></project-board>
            <project-file-modal></project-file-modal>
        </div>
    </div>
@endsection