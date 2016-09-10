@extends('layouts.app')
@section('content')
    <div id="file-history" class="container">
        <div id="page-fixed-top" class="container-no-gutter">
            <h3>
                <span class="small">Version History</span>
                <br>
                {{ $fileRequest->name }}
                <br>
                <span class="small"><a
                            href="/checklist/{{$fileRequest->checklist->hash}}">{{ $fileRequest->checklist->name }}</a></span>
            </h3>
        </div>
        <div id="page-scroll-content">
            <!-- Versions Table -->
            <table class="table table-standard table-hover">
                <thead>
                <tr>
                    <th>File</th>
                    <th>Reason / Changes</th>
                    <th></th>
                <tr>
                </thead>
                <tbody>
                @foreach($fileRequest->uploads as $index => $upload)
                    <tr>
                        <td class="fit-to-content no-wrap">{{ $upload->file_name }}</td>
                        <td>
                            @if($reason = $upload->rejected_reason )
                                {{ $reason }}
                            @else
                                --
                            @endif
                        </td>
                        <td class="fit-to-content">
                            <a href="{{ awsURL() . $upload->path }}">
                                @if(count($fileRequest->uploads) === ($index + 1))
                                    <button type="button" class="btn btn-download btn-info btn-small"><i class="fa fa-download"></i>
                                        Latest</button>
                                @else
                                    <button type="button" class="btn btn-download btn-info btn-small"><i class="fa fa-download"></i>
                                        Version {{ $index + 1 }}</button>
                                @endif
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection