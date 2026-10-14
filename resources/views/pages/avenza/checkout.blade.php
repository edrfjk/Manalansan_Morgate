@extends('layouts.app')
@section('title', 'Checkout')
@section('content')

@php
    $canOrder = $customer && $customer->address && $customer->status === 'active';
@endphp

<div class="avz-head">
    <div>
        <div class="avz-head__eyebrow">FINAL STEP</div>
        <h2>Checkout</h2>
        <p class="avz-head__sub">Review your order before it goes to the shop</p>
    </div>
    <div>
        <a href="{{ url('/avenza/cart') }}" class="btn btn-white">Back to cart</a>
    </div>
</div>

<div class="wrapper wrapper-content">
    <div class="animated fadeIn">

        <form action="{{ url('/avenza/checkout') }}" method="post">
            @csrf
            <div class="row">

                <div class="col-lg-8">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="avz-rule">Your order</div>

                            @foreach($lines as $l)
                                <div class="avz-line">
                                    <div class="avz-line__media">
                                        @if($l->photo)
                                            <img src="{{ asset('_uploads/'.$l->photo) }}" alt="">
                                        @else
                                            <div class="avz-line__media--empty"><i class="fa fa-picture-o"></i></div>
                                        @endif
                                    </div>
                                    <div class="avz-line__body">
                                        <div class="avz-line__room">{{ strtoupper($l->category_name ?? 'AVENZA') }}</div>
                                        <div class="avz-line__name">{{ $l->product_name }}</div>
                                        <div class="text-muted" style="font-size:13px;">
                                            {{ $l->quantity }} &times; &#8369;{{ number_format($l->price, 2) }}
                                        </div>
                                    </div>
                                    <div class="avz-line__right">
                                        <span class="price" style="font-size:17px;">
                                            &#8369;{{ number_format($l->line_total, 2) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="avz-rule">Delivery</div>

                            <div class="row">
                                <div class="col-md-7">
                                    <label style="font-size:11px;letter-spacing:.05em;text-transform:uppercase;color:var(--muted);">
                                        Deliver to
                                    </label>

                                    @if($customer && $customer->address)
                                        <div class="avz-address">
                                            <strong>{{ $customer->customer_name }}</strong><br>
                                            {{ $customer->contact_name ?: '' }}<br>
                                            {{ $customer->address }}<br>
                                            {{ $customer->city }} {{ $customer->postal_code }}<br>
                                            {{ $customer->country }}
                                        </div>
                                    @else
                                        <div class="avz-address avz-address--warn">
                                            No delivery address on file yet. Add one before placing
                                            this order, or the shop has nowhere to send it.
                                        </div>
                                    @endif

                                    <a href="{{ url('/avenza/profile') }}" class="btn btn-white btn-sm mt-2">
                                        {{ $customer && $customer->address ? 'Edit details' : 'Add details' }}
                                    </a>
                                </div>

                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="shipper_id">Preferred courier</label>
                                        <select name="shipper_id" id="shipper_id" class="form-control">
                                            <option value="">Let the shop decide</option>
                                            @foreach($shippers as $s)
                                                <option value="{{ $s->shipper_id }}">{{ $s->shipper_name }}</option>
                                            @endforeach
                                        </select>
                                        <p class="text-muted mt-2">
                                            <small>The shop confirms the courier once your order is accepted.</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="avz-summary">
                        <div class="avz-summary__row">
                            <span>Items</span>
                            <span>{{ collect($lines)->sum('quantity') }}</span>
                        </div>
                        <div class="avz-summary__row">
                            <span>Payment</span>
                            <span>On confirmation</span>
                        </div>

                        <div class="avz-summary__total">
                            <span>Total</span>
                            <strong>&#8369;{{ number_format($total, 2) }}</strong>
                        </div>

                        @if($canOrder)
                            <button class="btn btn-primary btn-block" type="submit">Place order</button>
                            <p class="avz-summary__note">
                                Your order goes to the shop as pending. Payment and delivery
                                are arranged directly with Avenza.
                            </p>
                        @else
                            <a href="{{ url('/avenza/profile') }}" class="btn btn-primary btn-block">
                                Add delivery details
                            </a>
                            <p class="avz-summary__note">
                                @if($customer && $customer->status !== 'active')
                                    Your account can't place orders right now. Please contact the shop.
                                @else
                                    We need a delivery address before this order can be placed.
                                @endif
                            </p>
                        @endif
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>
@endsection