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
            <table class="table table-standard">
                <thead>
                <tr>
                    <th @if($sort === 'required')class="current_{{  $order }}"@endif>
                        <a href="/checklist/{{ Hashids::encode($checklist->id) }}?sort=required&order={{ $order === 'asc' ? 'desc' : 'asc' }}">
                            Required
                        </a>
                    </th>
                    <th @if($sort === 'name')class="current_{{  $order }}"@endif>
                        <a href="/checklist/{{ Hashids::encode($checklist->id) }}?sort=name&order={{ $order === 'asc' ? 'desc' : 'asc' }}">
                            Name
                        </a>
                    </th>
                    <th @if($sort === 'version')class="current_{{  $order }}"@endif>
                        <a href="/checklist/{{ Hashids::encode($checklist->id) }}?sort=version&order={{ $order === 'asc' ? 'desc' : 'asc' }}">
                            Version
                        </a>
                    </th>
                    <th @if($sort === 'due')class="current_{{  $order }}"@endif>
                        <a href="/checklist/{{ Hashids::encode($checklist->id) }}?sort=due&order={{ $order === 'asc' ? 'desc' : 'asc' }}">
                            Due
                        </a>
                    </th>
                    <th @if($sort === 'status')class="current_{{  $order }}"@endif>
                        <a href="/checklist/{{ Hashids::encode($checklist->id) }}?sort=status&order={{ $order === 'asc' ? 'desc' : 'asc' }}">
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
                            @if($file->rejected)
                                rejected
                            @elseif($file->path)
                                received
                            @else
                                not received
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-solid-green"><i class="fa fa-upload"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection