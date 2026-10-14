@extends('layouts.app')
@section('title', 'My Profile')
@section('content')

@php $status = $customer->status ?? 'active'; @endphp

<div class="avz-head">
    <div>
        <div class="avz-head__eyebrow">YOUR ACCOUNT</div>
        <h2>My profile</h2>
        <p class="avz-head__sub">Where your orders get delivered</p>
    </div>
</div>

<div class="wrapper wrapper-content">
    <div class="animated fadeIn">
        <div class="row">

            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-content">

                        @if(! ($customer->address ?? null))
                            <div class="avz-address avz-address--warn mb-4">
                                You'll need a delivery address before you can place an order.
                            </div>
                        @endif

                        <form action="{{ url('/avenza/profile') }}" method="post">
                            @csrf
                            @method('PATCH')

                            <div class="avz-rule">Who you are</div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label for="customer_name">Full name</label>
                                    <input type="text" class="form-control" name="customer_name" id="customer_name"
                                           value="{{ old('customer_name', $customer->customer_name ?? auth()->user()->name) }}" required>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label for="contact_name">Contact number</label>
                                    <input type="text" class="form-control" name="contact_name" id="contact_name"
                                           value="{{ old('contact_name', $customer->contact_name ?? '') }}"
                                           placeholder="09xx xxx xxxx">
                                </div>
                            </div>

                            <div class="avz-rule mt-4">Where to deliver</div>

                            <div class="form-group mb-3">
                                <label for="address">Street address</label>
                                <input type="text" class="form-control" name="address" id="address"
                                       value="{{ old('address', $customer->address ?? '') }}"
                                       placeholder="House number, street, barangay">
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label for="city">City or municipality</label>
                                    <input type="text" class="form-control" name="city" id="city"
                                           value="{{ old('city', $customer->city ?? '') }}">
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label for="postal_code">Postal code</label>
                                    <input type="text" class="form-control" name="postal_code" id="postal_code"
                                           value="{{ old('postal_code', $customer->postal_code ?? '') }}">
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label for="country">Country</label>
                                    <input type="text" class="form-control" name="country" id="country"
                                           value="{{ old('country', $customer->country ?? 'Philippines') }}">
                                </div>
                            </div>

                            <hr>
                            <button class="btn btn-primary" type="submit">Save profile</button>
                            <a href="{{ url('/avenza') }}" class="btn btn-secondary">Back to shop</a>
                        </form>

                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="avz-rule">Account</div>

                        <dl class="avz-dl">
                            <dt>Email</dt>
                            <dd>{{ auth()->user()->email }}</dd>

                            <dt>Status</dt>
                            <dd>
                                <span class="avz-pill avz-pill--{{ $status }}">{{ ucfirst($status) }}</span>
                            </dd>

                            <dt>Member since</dt>
                            <dd>{{ \Carbon\Carbon::parse(auth()->user()->created_at)->format('F Y') }}</dd>
                        </dl>

                        @if($status !== 'active')
                            <div class="avz-address avz-address--warn mt-3">
                                Your account can't place orders right now. You can still browse.
                                Contact the shop to sort this out.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="ibox">
                    <div class="ibox-content">
                        <div class="avz-rule">Your orders</div>
                        <a href="{{ url('/avenza/orders') }}" class="btn btn-white btn-block btn-sm">
                            View order history
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection