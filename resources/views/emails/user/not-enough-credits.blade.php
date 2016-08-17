@extends('emails.partials.standard')

@section('header-color', '#FFA339')
@section('header-text', 'Not Enough Credits')
@section('subheading', 'Oops!')
@section('summary', 'We couldn\'t create your last checklist because you\'ve ran out of credits.')
@section('body')
    <div>
        <p>
            To keep on using Files Collector, you'll have to do one of the following:
        </p>
        <br>
        <ol>
            <li>
                Subscribe for $15 / month (unlimited lists)
            </li>
            <li>
                Ask recipients to join (bonus credits)
            </li>
            <li>
                Wait for next month (5 free lists)
            </li>
        </ol>
    </div>
@endsection
@section('button-link')
    {{ env('DOMAIN') }}/account
@endsection
@section('button-text', 'My Account')