Hi there,
<br>
<br>
{{ $maker->name }}({{ $maker->email }}) has created a checklist of files to get from you.
<br>
<br>
<strong>If you have no idea what this is pertaining to, please ignore this email. We're really sorry for taking up your time.</strong>
<br>
<br>
If you do know what we're talking about you can check out the list of files at: <a href="{{ env('DOMAIN') }}/checklist/{{ hashId($checklist) }}">{{ $checklist->name }}</a>. There you can upload the necessary files, read notes for changes required and immediately see which files {{ $maker->name }} still needs from you.
<br>
<br>
Periodically, we'll send you a few reminders for upcoming files as well as overdue ones. If you would like to turn off notifications, you can do that pretty quickly on the checklist page.
<br>
<br>
Just one last thing, the link we've sent you is private and was made special for you. Please don't share it with anybody unless {{ $maker->name }} knows you're doing so.
<br>
<br>
@include('emails.partials.signature')

