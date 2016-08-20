<!DOCTYPE html>
<html lang="en">
<head>

    @include('layouts.partials.html-head')


    <link href="{{ asset('/css/landing.css') }}" rel="stylesheet">

    @include('trackers.fb-pixel')
    @include('fonts.type-kit')

</head>

<body>




<div id="landing" v-cloak>

    @include('layouts.partials.nav')

    <div id="hero">
        <div id="hero-content">
            <h1>
                Web service for
                <br>
                professionals who
            </h1>
            <ul class="list-unstyled">
                <li>
                    Regularly asks for lots of files
                </li>
                <li>Have long project schedules</li>
                <li>Don't enjoy chasing people up</li>
            </ul>
        </div>
    </div>

    <div class="container">

        <div id="tagline">
            <h1 class="text-center">What we do</h1>
            <h3 class="text-center">We help you keep track of received files, send reminders <span class="no-wrap">(so you don't have
                to)</span>,
                <br> manage change requests and keep your files organized.</h3>
        </div>


        <div id="features" class="row text-center">
            <div class="col-sm-4">
                <img src="/images/icons/cabinet.svg">
                <h4>Organized</h4>
                <p>
                    Sortable, searchable, file versioning and shared comments. Find an exact file or view progress in
                    seconds.
                </p>
            </div>
            <div class="col-sm-4">
                <img src="/images/icons/smile.svg">
                <h4>Friendly</h4>
                <p>
                    Nobody likes sending reminders. Let us handle it for you, so you can spend your time building
                    relationships instead.
                </p>
            </div>
            <div class="col-sm-4">
                <img src="/images/icons/pie.svg">
                <h4>Easy</h4>
                <p>
                    Up and running in less than 10 seconds. Don't believe us? Scroll down to create your first list.
                </p>
            </div>
        </div>
    </div>


    <div id="list-maker">
        <div class="container">
            <div class="content">
                <h2 class="text-center">Try it out (make a checklist)</h2>
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
        </div>
    </div>



    <div class="container">

        <div id="pricing">
            <h1 class="text-center">Pricing</h1>
            <div class="row text-center">
                <div id="price-free" class="col-sm-6 price">
                    <h3>Free</h3>
                    <img src="/images/chimp_xs.jpg" alt="Chimp pic">
                    Create up to 5 lists per month.
                </div>
                <div id="price-paid" class="col-sm-6 price">
                    <h3>$15 / month</h3>
                    <img src="/images/gorilla_xs.jpg" alt="Chimp pic">
                    Unlimited lists - an offer you can't refuse.
                </div>
            </div>
        </div>

    </div>

    <div id="email-maker">
        <div class="container">
            <h2 class="text-center">Make lists on the fly</h2>
            <h4 class="text-center">cc: lists@in.filescollector.com</h4>
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
                <div class="col-sm-4 right">
                    <h3>Everywhere You Go</h3>
                    <p>Create lists whenever you want. As long as you can send an email, we got your back.</p>
                    <br>
                    <h3>Formatting</h3>
                    <ol>
                        <li>- before each file name</li>
                        <li>One file per line</li>
                        <li>Due date (if any) in square brackets: [dd/mm/yyyy]</li>
                    </ol>
                    <h4 class="text-center">Basically, list out the files as usual and we'll handle the rest.</h4>
                </div>
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
