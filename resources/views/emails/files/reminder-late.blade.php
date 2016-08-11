Hi again,

Looks like you've got a few overdue files that {{ $checklist->user->name }} is expecting from you for: <a href="{{ env('DOMAIN') }}/checklist/{{ hashId($checklist) }}">{{ $checklist->name }}</a>.
<br>
<br>
<strong>Overdue Files ({{ $lateFiles->count() }})</strong>
<br>
<br>
<ul>
    @foreach($lateFiles as $files)
        <li>{{ $files->name }} [{{ $files->due->format('d M Y') }}]</li>
    @endforeach
</ul>
<br>
<br>
Once you're ready to upload the files and save the day head on over to the checklist page, <a href="{{ env('DOMAIN') }}/checklist/{{ hashId($checklist) }}?due=+{{ $today->subDay()->format('Y-m-d') }}">here</a>. <em>Otherwise, if you've decided against uploading the files and would like to stop receiving these reminders, you can do that on the checklist page too.</em>

@include('emails.partials.signature')