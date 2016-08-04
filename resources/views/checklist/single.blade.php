@extends('layouts.app')

@section('content')
    <div id="checklist-single" class="container">
        <h1 class="text-center text-capitalize">
            {{ $checklist->name }}
        </h1>
        @if($checklist->description)
            <p class="text-center">{{ $checklist->description }}</p>
        @endif
        <br>
        <form>
            <div class="form-group">
                <div class="input-group">
                    <input type="text" placeholder="Search name..." class="form-control" name="search" value="{{ $search }}">
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-solid-blue">Search</button>
                    </div>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <!-- Files Table -->
            <table id="table-files" class="table table-standard">
                <thead>
                <tr>
                    <th @if($sort === 'required')class="current_{{  $order }}"@endif>
                        <a href="/checklist/{{ hashId($checklist) }}?sort=required&order={{ $order === 'asc' ? 'desc' : 'asc' }}">
                            Required
                        </a>
                    </th>
                    <th @if($sort === 'name')class="current_{{  $order }}"@endif>
                        <a href="/checklist/{{ hashId($checklist) }}?sort=name&order={{ $order === 'asc' ? 'desc' : 'asc' }}">
                            Name
                        </a>
                    </th>
                    <th @if($sort === 'version')class="current_{{  $order }}"@endif>
                        <a href="/checklist/{{ hashId($checklist) }}?sort=version&order={{ $order === 'asc' ? 'desc' : 'asc' }}">
                            Version
                        </a>
                    </th>
                    <th @if($sort === 'due')class="current_{{  $order }}"@endif>
                        <a href="/checklist/{{ hashId($checklist) }}?sort=due&order={{ $order === 'asc' ? 'desc' : 'asc' }}">
                            Due
                        </a>
                    </th>
                    <th @if($sort === 'status')class="current_{{  $order }}"@endif>
                        <a href="/checklist/{{ hashId($checklist) }}?sort=status&order={{ $order === 'asc' ? 'desc' : 'asc' }}">
                            Status
                        </a>
                    </th>
                    <th></th>
                <tr>
                </thead>
                <tbody>
                @foreach($files as $file)
                    <tr>
                        <td class="fit-to-content text-center">
                            @if($file->required)
                                <i class="fa fa-check text-success"></i>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $file->name }}</td>
                        <td>{{ $file->version }}</td>
                        <td>
                            @if($file->due)
                                {{ $file->due->format('d M Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="file-status {{ $file->status }}">{{ $file->status }}</span>
                        </td>
                        <td class="col-upload">
                            <button type="button" class="btn btn-solid-green button-upload-file" data-file="{{ $file->id }}"><i class="fa fa-upload"></i></button>
                            <input id="input-file-{{  $file->id }}" type="file" name="file" data-url="/checklist/{{ hashId($checklist) }}/file/{{ $file->id }}" class="input-file-upload hide">
                            {{--<div class="file-upload-progress">--}}
                            {{--<div class="bar" style="width: 0%;"></div>--}}
                            {{--</div>--}}
                            <div class="progress">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                    <span class="sr-only">0%</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="results-controls">
                <div class="per-page">
                    <form>
                        <select name="per_page" onchange="this.form.submit()" class="form-control">
                            <option value="20" @if($perPage === '20')selected @endif >20</option>
                            <option value="60" @if($perPage === '60')selected @endif >60</option>
                            <option value="100" @if($perPage === '100')selected @endif >100</option>
                        </select>
                    </form>
                </div>
                <div class="paginator">
                    {{ $files->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection