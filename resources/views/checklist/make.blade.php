@extends('layouts.app')

@section('content')
    <div id="checklist-make" class="container">
            <checklist-maker :user="{{ Auth::user() }}"></checklist-maker>
    </div>
@endsection
