<!DOCTYPE html>
<html lang="en">
<head>

    @include('layouts.partials.html-head')


    <link href="{{ asset('/css/landing.css') }}" rel="stylesheet">

    @include('trackers.fb-pixel')
    @include('fonts.type-kit')
    @include('trackers.ga-tracker')

</head>

<body>


<div id="landing-start" v-cloak>
    @include('layouts.partials.nav', [
    'homeLink' => '/start'])
    <div id="elevator">
        <div class="container">
            <h1 class="text-center">
                We help you collect files & documents from clients, vendors or associates.
            </h1>
            <div class="row">
                <div class="col-xs-8 col-xs-offset-2">
                    <h3 class="text-center hidden-xs">
                        Scroll down if you regularly request lots of files, have long project lead times and enjoy
                        optimizing your work flow.
                    </h3>
                </div>
            </div>
        </div>
    </div>
    <div id="main-course">
        <div class="container">
            <h1 class="text-center">How we'll get it done</h1>
            <div class="row feature">
                <div class="col-sm-2 col-sm-offset-1">
                    <img src="/images/icons/alarm-clock.svg" alt="Alarm clock icon">
                </div>
                <div class="col-sm-7 col-sm-offset-1">
                    <h3 class="text-center">
                        Send Reminders
                        <br>
                        <small>
                            (so you don't have to)
                        </small>
                    </h3>
                    <p class="text-center hidden-xs">
                        We'll do it for upcoming and late files. You won't come off as pushy and you'll still get your documents on time.
                    </p>
                </div>
            </div>
            <div class="row feature">
                <div class="col-sm-2 col-sm-offset-1">
                    <img src="/images/icons/archive.svg" alt="Alarm clock icon">
                </div>
                <div class="col-sm-7 col-sm-offset-1">
                    <h3 class="text-center">
                        Organize and Track
                    </h3>
                    <p class="text-center hidden-xs">
                        At a glance, see which files have been received and which haven't. Or find the file you're looking for without hunting through your inbox. If you have massive lists of required documents from contractors that are all due in different months, you'll appreciate this.
                    </p>
                </div>
            </div>
            <div class="row feature">
                <div class="col-sm-2 col-sm-offset-1">
                    <img src="/images/icons/checklist.svg" alt="Alarm clock icon">
                </div>
                <div class="col-sm-7 col-sm-offset-1">
                    <h3 class="text-center">
                        Manage Change Requests
                    </h3>
                    <p class="text-center hidden-xs">
                        Removes a lot of the unecessary email boiler-plate every time you need something updated. Plus, we'll also track versions for you so everybody's always on the same page.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div id="using">
        <div id="by-email">
            <div class="container">
                <h1 class="text-center">
                    "That sounds cool and all but we already use heaps of SaaS apps and adding another tool into our workflow takes way too much effort."
                </h1>
                <h3 class="text-center">We think so too. That's why we made creating file requests so easy, it's already a part of your workflow.
                    <br>
                    <strong>cc: list@in.filescollector.com</strong>
                    <small>(write this down)</small>
                    <br>
                </h3>
                <div class="text-center">
                    <button id="button-show-example" type="button" class="btn btn-solid-green" @click="showEmailExample">Example</button>
                </div>
                <form id="form-email" v-show="emailExample">
                    <h5 class="text-muted text-center">Put a - before each file name. Keep each file on a single line. And put due dates in [square brackets]</h5>
                    <div class="form-group inline-label">
                        <label>To:</label>
                        <input type="text" value="john@example.com" class="form-control" disabled>
                    </div>

                    <div class="form-group inline-label">
                        <label>Cc:</label>
                        <input type="text" value="list@in.filescollector.com" class="form-control" tabindex="-1">
                    </div>

                    <div class="form-group inline-label">
                        <label>Subject:</label>
                        <input type="text" value="Files Needed For Project Update" class="form-control" disabled>
                    </div>

                    <textarea class="form-control" rows="15" tabindex="-1">Hi John,

Great work at the investors meeting, you really brought your A-game.

As we discussed last Tuesday, here is the list of documents I'll need you to prepare:

- Land survey report [12/09/2016]
- Budget reports for last quarter
- Sales contract with Apex co. ltd. [01/10/2016]

Thanks,
Mike</textarea>

                    <br>
                    <br>
                </form>
            </div>
        </div>
        <div id="by-maker">
            <div class="container">
                <h3 class="text-center">If you want to create file lists manually without emails, you can do that too, with our <a href="#" @click.prevent="showListMaker">list maker</a>.</h3>
                <div id="list-maker" v-show="listMaker">
                    <form-errors></form-errors>
                    @include('checklist.make.form')
                    <div class="text-right">
                        <button type="button" class="btn btn-solid-green" @click="showRegisterModal" :disabled="
                    ! canSendChecklist">Create List</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="cost">
        <div class="container">
            <h1>We found that 78% of our users only make 2 or 3 lists a month. So, we made it <strong>free for up to 5 lists</strong>, every month. And for only <strong>$15 per month, you get unlimited lists</strong>. This lets us keep everyone happy without compromising on delivery speed or storage space.</h1>
        </div>
    </div>

    <div id="thanks">
        <div class="container">
            <h1>You made it!</h1>
            <div class="text-center">
                <a href="/register" class="visible-xs"><button type="button" class="btn btn-solid-green btn-lg">Free Sign Up</button></a>
            </div>
            <h3 class="hidden-xs">Congrats, you've made it this far without bouncing! Since you're already here, you might as well <a href="/register">sign up</a> for an account (did we mention it was free?) and see for yourself how we'll help you be more pro.</h3>
        </div>
    </div>
    <registration-modal></registration-modal>
</div>

<script type="text/javascript" src="{{ asset('/js/landing/vendor.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/landing/dependencies.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/landing/start.js') }}"></script>
</body>
</html>
