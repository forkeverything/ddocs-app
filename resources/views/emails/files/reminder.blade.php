Hello again!

Just dropping in by to let you know that {{ $maker->name }} is still waiting on a few files from you for the following list:
<br>
<br>
<a href="{{ env('DOMAIN') }}/checklist/{{ hashId($checklist) }}"><strong>{{ $checklist->name }}</strong></a>
<br>
<br>
<strong>Breakdown of needed files:</strong>
<br>
<br>
Upcoming Files ({{ $upcomingFiles->count() }})
@if($upcomingFiles)
    <ul>
        @foreach($upcomingFiles as $files)
            <li>{{ $files->name }} [{{ $files->due->format('d M Y') }}]</li>
        @endforeach
    </ul>
@endif
<br>
<br>
Overdue Files ({{ $lateFiles->count() }})
@if($lateFiles)
    <ul>
        @foreach($lateFiles as $files)
            <li>{{ $files->name }} [{{ $files->due->format('d M Y') }}]</li>
        @endforeach
    </ul>
@endif
<br>
<br>
<br>
<strong>If you want to stop receiving emails for remaining files, head over to the checklist page and follow the simple instructions to turn off recipient notifications.</strong>

@include('emails.partials.signature')
