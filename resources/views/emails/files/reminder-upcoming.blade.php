@extends('emails.partials.standard')

@section('header-color', '#3498DB')
@section('header-text', 'Upcoming Files')
@section('subheading'){{ $checklist->name }}@endsection
@section('summary', 'You have files due soon for the above checklist')
@section('body')
    <div>
        <strong>Upcoming Files ({{ count($upcomingFiles) }})</strong>
        <br>
        <ul>
            @foreach($upcomingFiles as $files)
                <li>{{ $files->name }} [{{ $files->due->format('d M Y') }}]</li>
            @endforeach
        </ul>
    </div>
@endsection
@section('button-link'){{ env('DOMAIN') }}/checklist/{{ hashId($checklist) }}@endsection
@section('button-text', 'View Checklist')