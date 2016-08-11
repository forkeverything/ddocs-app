Hello,
<br>
<br>
Dropping in to let you know that {{ $maker->name }} has extra kindly requested for changes to be made to the file: <strong>{{ $fileRequest->name }}</strong>.
<br>
<br>
<strong>Changes/Reason</strong>
<br>
<br>
{{ $fileRequest->uploads->last()->rejected_reason }}
<br>
<br>
You can re-upload the file as well as turn off notification emails by visiting the <a href="{{ env('DOMAIN') }}/checklist/{{ hashId($fileRequest->checklist) }}">checklist page</a>.

@include('emails.partials.signature')
