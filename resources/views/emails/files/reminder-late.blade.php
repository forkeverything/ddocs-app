@extends('emails.partials.standard')

@section('header-color', '#E74C3C')
@section('header-text', 'Overdue Files')
@section('subheading'){{ $checklist->name }}@endsection
@section('summary'){{ $checklist->user->name }} is still expecting a few files from you.@endsection
@section('body')
    <div>
        <strong>Overdue Files ({{ count($lateFiles) }})</strong>
        <br>
        <ul>
            @foreach($lateFiles as $files)
                <li>{{ $files->name }} [{{ $files->due->format('d M Y') }}]</li>
            @endforeach
        </ul>
    </div>
@endsection
@section('button-link'){{ env('DOMAIN') }}/checklist/{{ hashId($checklist) }}@endsection
@section('button-text', 'View Checklist')