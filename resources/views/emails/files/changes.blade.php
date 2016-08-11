Hello,
<br>
<br>
Just dropping in to let you know that {{ $maker->name }} has extra kindly requested for changes to be made to {{ $fileRequest->name }}.
<br>
<br>
<strong>Changes/Reason</strong>
<br>
{{ $fileRequest->uploads->latest()->rejected_reason }}
<br>
<br>
You can re-upload the file as well as turn off notification emails by visiting the <a href="{{ env('DOMAIN') }}/checklist/{{ hashId($fileRequest->checklist) }}">checklist page</a>.
<br>
<br>
@include('emails.partials.signature')
