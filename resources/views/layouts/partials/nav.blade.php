<nav id="top-nav" class="navbar navbar-default navbar-static-top">
    <div class="container">
        <ul class="nav navbar-nav navbar-right">
            @if(Auth::check())
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle text-capitalize" data-toggle="dropdown" role="button"
                       aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Dashboard</a></li>
                        <li><a href="/logout" alt="link to logout">Logout</a></li>
                    </ul>
                </li>
            @else
                <li>
                    <a href="/login" class="navbar-link">
                        Login
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>