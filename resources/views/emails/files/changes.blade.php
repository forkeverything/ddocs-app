@extends('emails.partials.standard')

@section('header-color', '#353535')
@section('header-text', 'Changes Required')
@section('subheading'){{ $fileRequest->name }}@endsection
@section('summary'){{ $maker->name }} has requested changes be made to the above file.@endsection
@section('body')
    <div>
        <strong>Reason</strong>
        <br>
        <p>
            {{ $fileRequest->uploads->last()->rejected_reason }}
        </p>
        <br>
        <p>
            After any changes have been made you can re-upload the file at the checklist page.
        </p>
    </div>
@endsection
@section('button-link'){{ env('DOMAIN') }}/checklist/{{ hashId($fileRequest->checklist) }}@endsection
@section('button-text', 'View Checklist')