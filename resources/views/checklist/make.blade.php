@extends('layouts.app')

@section('content')
    <div id="checklist-make" class="container">

        <div id="page-fixed-top" class="container-no-gutter">
            <div class="header">
                <h3 class="text-center"><strong>New Checklist</strong></h3>
                <p class="text-center">
                    Create a list of all the files that you need along with their due dates (if any) and
                    we'll handle the rest.
                </p>
            </div>
        </div>

        <div id="page-scroll-content">
            <checklist-maker :user="{{ Auth::user() }}"></checklist-maker>
        </div>

    </div>
@endsection
