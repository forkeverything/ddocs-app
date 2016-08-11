Hi {{ $user->name }},
<br>
<br>
Yay! Thanks for creating an account with us!
<br>
<br>
<strong>What happens now?</strong>
<br>
Now, whenever you create a list of files you need from someone at filescollector.com, we'll gently work our magic to get you said files. We'll start by kindly letting the recipient know that you need some files from them.
And when they have some due files coming up, we'll send some nice messages to just let them know. Of course, when they're running a bit late, we'll give them a tiny nudge too.
Last but not least, we'll even sort and index it all neatly so everybody can see what's up and make everything is searchable.
<br>
<br>
<strong>Sounds good let's get started!</strong>
<br>
Awesome! Just head on over to <a href="{{ env('DOMAIN') }}/checklist/make">make a checklist</a>to make your first checklist.
If you've already made a checklist, sit back, enjoy your americano (or chai latte if that's how you roll) and let us take care of the rest.
We'll send you another email as soon as we're done collecting all your files.
<br>
<br>
We hope you enjoy using our service. Thanks again for helping us get one step closer to a world
where no files are left behind, or forgotten.
<br>
<br>
@include('emails.partials.signature')