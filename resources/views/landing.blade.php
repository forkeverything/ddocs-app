<!DOCTYPE html>
<html lang="en">
<head>

    @include('layouts.partials.html-head')


    <link href="{{ asset('/css/landing.css') }}" rel="stylesheet">
</head>

<body>

@include('layouts.partials.nav')

<div id="landing" class="container" v-cloak>

    <div class="jumbotron">
        <h1>Collecting files and kicking ass.</h1>
        <p>Asking for files from other people is way harder than it should be. We make it easy.</p>
        <p><a href="#" class="btn btn-solid-blue btn-lg">Show Me</a></p>
    </div>

    <h1 class="text-center">On time, every time.</h1>
    <h3 class="text-center">Our software helps you keep track of received files, send reminders (so you don't have to)
        <br>and manage revisions or updates.</h3>


    <div id="features" class="row text-center">
        <div class="col-sm-4">
            <h4>Organized</h4>
            <p>
                Sortable, searchable, file versioning and shared comments. Find an exact file or view progress in
                seconds.
            </p>
        </div>
        <div class="col-sm-4">
            <h4>Friendly</h4>
            <p>
                Nobody likes sending reminders. Let us handle it for you, so you can spend your time building
                relationships instead.
            </p>
        </div>
        <div class="col-sm-4">
            <h4>Easy</h4>
            <p>
                Up and running in less than 10 seconds. Don't believe us? Scroll down to create your first list.
            </p>
        </div>
    </div>

    <div id="list-maker">
        <h3 class="text-center">Create Your First Checklist</h3>
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <form-errors></form-errors>
                @include('checklist.make.form')
                <div class="text-right">
                    <button type="button" class="btn btn-solid-green" @click="showRegisterModal" :disabled="
                    ! canSendChecklist">Create List</button>
                </div>
            </div>
        </div>
    </div>

    <div id="pricing">
        <h2 class="text-center">Pricing</h2>
        <div class="row text-center">
            <div class="col-sm-6">
                <h3>Free</h3>
                <img src="/images/chimp_xs.jpg" alt="Chimp pic">
                Create up to 5 lists per month.
            </div>
            <div class="col-sm-6">
                <h3>$5 / month</h3>
                <img src="/images/gorilla_xs.jpg" alt="Chimp pic">
                Unlimited lists.
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-center">Want to create lists even quicker?</h3>
        <h4 class="text-center">cc: lists@in.filescollector.com</h4>
        <p class="text-center">Using the same email you've registered with us. Just cc us in on any emails you're
            sending when you ask for files.</p>
        <br>
        <div class="row">
            <div class="col-sm-8">


                <form id="form-email">
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

                    <textarea class="form-control" rows="13" tabindex="-1">Hi John,

Great work at the investors meeting, you really brought your A-game.

As we've discussed last Tuesday, here is the list of documents I'll need you to prepare:

- Land survey report [12/09/2016]
- Budget reports for last quarter
- Sales contract with Apex co. ltd. [01/10/2016]

Regards,
Mike</textarea>

                    <br>
                    <br>
                </form>


            </div>
            <div class="col-sm-4">
                <h4>Everywhere You Go</h4>
                <p>Create lists whenever you want. As long as you can send an email, we'll have your back. Desktop,
                    laptop, mobile or those standing computers at airports.</p>
                <br>
                <h4>Formatting Rules</h4>
                <ol>
                    <li>- before each file name</li>
                    <li>Put file names on it's own line</li>
                    <li>Due date (if any) in square brackets: [dd/mm/yyyy]</li>
                </ol>
                <p>In other words, just create a list of files as you usually do and we'll handle the rest.</p>
            </div>
        </div>
    </div>


    <registration-modal></registration-modal>

</div>

<script type="text/javascript" src="{{ asset('/js/landing/vendor.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/landing/dependencies.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/landing/main.js') }}"></script>
</body>
</html>
