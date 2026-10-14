@extends('layouts.app')

@section('title', 'Log in or Sign up')

@section('content')

@php
    $as   = request('as', 'customer');
    $next = request('next', '/avenza');
    // Admin side never opens the signup tab, even if ?tab=signup is passed
    $tab  = $as === 'admin' ? 'login' : (old('form') ?: request('tab', 'login'));
@endphp

<x-page-header pagetitle="{{ $as === 'admin' ? 'Admin Access' : 'Customer Access' }}" class="bg-info"/>

<div class="wrapper wrapper-content">
    <div class="animated fadeInRightBig">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-content">

                        @if($as === 'admin')
                            <p class="text-muted">
                                Admin accounts are not self-service. Sign in with the account
                                issued by the shop owner.
                            </p>
                        @else
                            <p class="text-muted">
                                Sign in to browse furniture, add to cart, and place orders.
                            </p>
                        @endif

                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $tab === 'login' ? 'active' : '' }}"
                                   data-toggle="tab" href="#tab-login">Log in</a>
                            </li>
                            @if($as !== 'admin')
                            <li class="nav-item">
                                <a class="nav-link {{ $tab === 'signup' ? 'active' : '' }}"
                                   data-toggle="tab" href="#tab-signup">Sign up</a>
                            </li>
                            @endif
                        </ul>

                        <div class="tab-content mt-3">

                            {{-- ------------------------------------------ LOGIN --}}
                            <div id="tab-login" class="tab-pane {{ $tab === 'login' ? 'active' : '' }}">
                                <form action="{{ url('/login') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="form" value="login">
                                    <input type="hidden" name="next" value="{{ $next }}">

                                    <div class="form-group mb-3">
                                        <label for="login-email">Email</label>
                                        <input type="email" class="form-control" name="email" id="login-email"
                                               value="{{ old('form') === 'login' ? old('email') : '' }}"
                                               placeholder="you@example.com" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="login-password">Password</label>
                                        <input type="password" class="form-control"
                                               name="password" id="login-password" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label style="font-weight:400;">
                                            <input type="checkbox" name="remember"> Keep me signed in
                                        </label>
                                    </div>

                                    <button class="btn btn-primary" type="submit">Log in</button>
                                    <a href="{{ url('/') }}" class="btn btn-secondary">Back</a>
                                </form>
                            </div>

                            {{-- ----------------------------------------- SIGNUP --}}
                            {{-- Customer only. The pane itself is removed for admin,
                                 not just hidden, so it can't be un-hidden in devtools. --}}
                            @if($as !== 'admin')
                            <div id="tab-signup" class="tab-pane {{ $tab === 'signup' ? 'active' : '' }}">
                                <form action="{{ url('/register') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="form" value="signup">
                                    <input type="hidden" name="next" value="{{ $next }}">

                                    <div class="row">
                                        <div class="col-md-6 form-group mb-3">
                                            <label for="reg-name">Full name</label>
                                            <input type="text" class="form-control" name="customer_name" id="reg-name"
                                                   value="{{ old('form') === 'signup' ? old('customer_name') : '' }}" required>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <label for="reg-contact">Contact number</label>
                                            <input type="text" class="form-control" name="contact_name" id="reg-contact"
                                                   value="{{ old('form') === 'signup' ? old('contact_name') : '' }}"
                                                   placeholder="09xx xxx xxxx">
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="reg-email">Email</label>
                                        <input type="email" class="form-control" name="email" id="reg-email"
                                               value="{{ old('form') === 'signup' ? old('email') : '' }}" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="reg-address">Address</label>
                                        <input type="text" class="form-control" name="address" id="reg-address"
                                               value="{{ old('form') === 'signup' ? old('address') : '' }}">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 form-group mb-3">
                                            <label for="reg-city">City</label>
                                            <input type="text" class="form-control" name="city" id="reg-city"
                                                   value="{{ old('form') === 'signup' ? old('city') : '' }}">
                                        </div>
                                        <div class="col-md-4 form-group mb-3">
                                            <label for="reg-postal">Postal code</label>
                                            <input type="text" class="form-control" name="postal_code" id="reg-postal"
                                                   value="{{ old('form') === 'signup' ? old('postal_code') : '' }}">
                                        </div>
                                        <div class="col-md-4 form-group mb-3">
                                            <label for="reg-country">Country</label>
                                            <input type="text" class="form-control" name="country" id="reg-country"
                                                   value="{{ old('form') === 'signup' ? old('country', 'Philippines') : 'Philippines' }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 form-group mb-3">
                                            <label for="reg-password">Password</label>
                                            <input type="password" class="form-control"
                                                   name="password" id="reg-password" required>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <label for="reg-password2">Confirm password</label>
                                            <input type="password" class="form-control"
                                                   name="password_confirmation" id="reg-password2" required>
                                        </div>
                                    </div>

                                    <button class="btn btn-primary" type="submit">Create account</button>
                                    <a href="{{ url('/') }}" class="btn btn-secondary">Back</a>

                                    <p class="text-muted mt-3">
                                        <small>All new accounts are customer accounts.</small>
                                    </p>
                                </form>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection