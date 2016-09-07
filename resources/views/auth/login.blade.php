@extends('layouts.app')

@section('content')
    <div id="page-scroll-content">
        <div id="login" class="container">
            <div class="row">
                <div id="login-body" class="col-sm-6 col-sm-offset-3">
                    <h2 class="text-center">Login</h2>
                    <form id="form-login" role="form" method="POST" action="{{ url('/login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="control-label">Email</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">
                            @if ($errors->has('email'))
                                <span class="help-block">
                    {{ $errors->first('email') }}
                    </span>
                            @endif
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="control-label">Password</label>
                            <input id="password" type="password" class="form-control" name="password">
                            @if ($errors->has('password'))
                                <span class="help-block">
                    {{ $errors->first('password') }}
                    </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <a href="{{ url('/password/reset') }}">Forgot password?</a>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary form-control">
                                <i class="fa fa-btn fa-sign-in"></i> Login
                            </button>
                        </div>

                        <div class="form-group text-right">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember"> Remember Me
                                </label>
                            </div>
                            <p class="text-muted text-right">Don't have an account yet? <a href="/register">Sign up</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
