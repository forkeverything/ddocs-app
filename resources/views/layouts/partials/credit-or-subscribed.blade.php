@if(Auth::user()->subscription('main'))
    <span class="text-info" data-toggle="tooltip" data-placement="bottom" title="Subscribed">[s]</span>
@else
    <span class="text-muted" data-toggle="tooltip" data-placement="bottom" title="Credits">({{ Auth::user()->credits }})</span>
@endif