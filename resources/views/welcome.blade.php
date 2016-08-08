@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Welcome</div>

                    <div class="panel-body">
                        Your Application's Landing Page.

                        <h4><a href="/checklist/make">Make a checklist</a></h4>
                        @if(Auth::check() && Auth::user()->checklists)
                            <ul>
                                @foreach(Auth::user()->checklists as $checklist)
                                    <li>
                                        <a href="/checklist/{{ hashId($checklist) }}">{{ $checklist->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
