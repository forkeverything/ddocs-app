@extends('layouts.landing')
@section('content')
    <div id="landing-start">
            @include('layouts.partials.nav')
            <div id="elevator">
                <div class="container">
                    <h1 class="text-center">
                        We help you collect files & documents from clients, vendors or associates.
                    </h1>
                    <div class="row">
                        <div class="col-xs-8 col-xs-offset-2">
                            <h3 class="text-center hidden-xs">
                                Scroll down if you regularly request lots of files, have long project lead times or hate
                                late documents.
                            </h3>
                            <img src="/images/folder-bulb.svg" alt="hero folder image">
                        </div>
                    </div>
                </div>
            </div>
            <div id="main-course">
                <div class="container">
                    <h1 class="text-center">How it's done</h1>
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
                                We'll send tiny reminders for upcoming and late files. You get to be the good guy and
                                your documents will still be on time. <em>Win, freakin' win.</em>
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
                                Hate inbox hunting? Us too. Keeping tabs on files over months from different people is
                                hard (and annoying). That's why we keep it clean, centralized and awesome.
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
                                Need them to make a few changes and re-upload? No problemo. We'll send off the request
                                email for you and even keep a record of each version. It's so simple, you'll def love
                                it.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="using">
                <div id="by-email">
                    <div class="container">
                        <h1 class="text-center">
                            "That sounds cool and all but we already use a ton of SaaS apps and adding another tool into
                            our workflow takes way too much effort."
                        </h1>
                        <h3 class="text-center">We think so too. That's why we made creating file requests so easy that
                            it's already a part of your workflow! All you need to do is:
                            <br>
                            <strong>cc: list@in.filescollector.com</strong>
                            <small>(write this down)</small>
                            <br>
                        </h3>
                        <landing-email-form></landing-email-form>
                    </div>
                </div>
                <div id="by-maker">
                    <div class="container">
                        <landing-list-maker :user="{{ Auth::user() }}"></landing-list-maker>
                    </div>
                </div>
            </div>

            <div id="cost">
                <div id="grass"></div>
                <div class="container">
                    <h1>We found that 78% of our users only make 2 or 3 lists a month. So, we made it <strong>free for
                            up to 5 lists every month with up to 1GB storage space.</strong> And for only <strong>$15
                            per month, you get unlimited lists and 10GB storage.</strong> This lets us keep everyone
                        happy without compromising on delivery speed or storage space.</h1>
                </div>
            </div>

            <div id="thanks">
                <div class="container">
                    <h1>You made it!</h1>
                    <h3 class="hidden-xs">We heard you were looking for an easier way to handle your docs. You should <a
                                href="/register">sign up</a> for an account (did we mention it was free?) and see how we
                        can help.</h3>
                    <br>
                    <div class="text-center">
                        <a href="/register">
                            <button type="button" class="btn btn-solid-green btn-lg">Free Sign Up</button>
                        </a>
                    </div>
                </div>
            </div>

    </div>
@endsection