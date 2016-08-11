Good Morning!

Just dropping by to let you know that you have a few files due soon for {{ $checklist->user->name }}'s list: <a href="{{ env('DOMAIN') }}/checklist/{{ hashId($checklist) }}">{{ $checklist->name }}</a>.
<br>
<br>
<strong>Upcoming Files ({{ $upcomingFiles->count() }})</strong>
<br>
<br>
    <ul>
        @foreach($upcomingFiles as $files)
            <li>{{ $files->name }} [{{ $files->due->format('d M Y') }}]</li>
        @endforeach
    </ul>
<br>
<br>
<em>If you want to stop receiving emails for remaining files, head over to the checklist page and follow the simple instructions to turn off recipient notifications.</em>

@include('emails.partials.signature')
