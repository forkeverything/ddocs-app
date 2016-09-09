@extends('layouts.app')

@section('content')
    <checklist-single :user="{{ Auth::user() }}" :checklist="{{ $checklist }}" :checklist-hash="'{{ hashId('checklist', $checklist) }}'" :aws-url="'{{ awsURL() }}'"></checklist-single>
@endsection